<?php

namespace App\Services\Routine;

use App\Models\Contact;
use App\Models\ContactList;
use App\Models\MessageSendingLog;
use App\Models\Scheduling;
use App\Models\Triggering;
use App\Models\TriggeringMessage;
use App\Trait\EvolutionTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class RoutineService
{

    use EvolutionTrait; 
    
    public function handleMessage()
    {
        try {
            $schedules = Scheduling::whereBetween('datetime', [
                    Carbon::now()->subMinutes(3), 
                    Carbon::now()
                ])
                ->where('status', 'Waiting')
                ->doesntHave('messageSendingLog')                
                ->get();

            if(!isset($schedules) || !count($schedules)) return;

            Log::info("Iniciando disparo de mensagens. Agendamentos: ". count($schedules));

            foreach ($schedules as $schedule) {
                try {
                    $this->prepareEvoCredentials();
                    switch ($schedule->midia) {
                        case 'video':
                            $this->sendMideaWithEvolution($schedule, 'video');
                            break;
                        case 'imagem':
                            $this->sendMideaWithEvolution($schedule, 'image');
                            break;
                        case 'audio':
                            $this->sendAudioWithEvolution($schedule);
                            break;
                        default:
                            $this->sendMessageWithEvolution($schedule);
                    }
                    
                    MessageSendingLog::create([
                        'schedule_id' => $schedule->id,
                        'instanceName' => $schedule->instance->name ?? 'noName',
                        'description' => $schedule->description,
                        'datetime' => $schedule->datetime,
                        'group_id' => $schedule->group_id,
                    ]);

                    $schedule->status = 'Sent';
                    $schedule->save();

                } catch (Exception $error) {
                    Log::error($error->getMessage());
                }
            }

        } catch (Exception $error) {
            Log::error($error->getMessage());
        }
    }

    public function sendTriggering()
    {
        try {
            $triggerings = Triggering::where('status', 'Pending')->get();
    
            foreach ($triggerings as $triggering) {
                $baseUrl = $triggering->evo_url;
                $apiKey = $triggering->evo_key;
                $instance = $triggering->evo_instance;
                $interval = (int) $triggering->interval;
    
                $contact = Contact::where('contact_list_id', $triggering->contact_list_id)
                    ->where('is_whatsapp', 'Pending')
                    ->first();
    
                if (!$contact) {
                    continue;
                }
    
                $lastContact = Contact::where('contact_list_id', $triggering->contact_list_id)
                    ->where('is_whatsapp', '!=', 'Pending')
                    ->orderBy('updated_at', 'desc')
                    ->first();
    
                if ($lastContact) {
                    $lastUpdated = $lastContact->updated_at;
                    $now = now();
    
                    $differenceInMinutes = $lastUpdated->diffInMinutes($now);
    
                    if ($differenceInMinutes < $interval) {
                        continue;
                    }
                }

                $phone = $this->preparePhone('(83) 9123-6636');
    
                $response = $this->checkNumberTriggering(
                    $baseUrl,
                    $apiKey,
                    $instance,
                    $phone
                );
    
                $message = $triggering->messages->random()->message;
                $name = explode(' ', $contact->name)[0];
                $message = str_replace('{nome}', ucfirst($name), $message);

                Log::info(json_encode($response));
    
                if ($response[0]['exists']) {
                    $contact->is_whatsapp = 'Whatsapp';
                    $contact->save();

                    $responseMidea = $this->sendMediaTriggering(
                        $baseUrl,
                        $apiKey,
                        $instance,
                        '+55' . $contact->phone,
                        $triggering->path,                        
                        $message,
                        'image',
                    );

                    $responseMidea;
    
                } else {
                    $contact->is_whatsapp = 'NotFound';
                    $contact->save();
                }
            }

            $this->updateProcessedTriggerings();

        } catch (Exception $error) {
            Log::error($error->getMessage());
        }
    }

    private function updateProcessedTriggerings()
    {
        $triggerings = Triggering::where('status', 'Pending')->get();

        foreach ($triggerings as $triggering) {
            $pendingContacts = Contact::where('contact_list_id', $triggering->contact_list_id)
                ->where('is_whatsapp', 'Pending')
                ->exists();

            if (!$pendingContacts) {
                $triggering->status = 'Finished';
                $triggering->save();
            }
        }
    }

    public function sendMideaWithEvolution($schedule, $type)
    {
        $instance = $schedule->instance->name;
        $numbers = explode(',', $schedule->group_id);
        $mediaType = $type;
        $media = $type === 'video' ? $schedule->video_path : $schedule->image_path;
        $media_dir = storage_path('app/public' . explode('/storage',$media)[1]);
        $mimeType = mime_content_type($media_dir);
        $fileExtension = pathinfo($media_dir, PATHINFO_EXTENSION);
        $fileName = "midea_automation.{$fileExtension}";
        if(isset($schedule->link)){
            $caption = str_replace('{{link}}', $schedule->link->url, $schedule->text);
        }else{
            $caption = $schedule->text;
        }
        $mention = !!$schedule->mention;
        foreach($numbers as $number){
            $number = trim($number);
            $this->sendMedia($instance, $number, $mediaType, $media, $caption, $mimeType, $fileName, mention: $mention);
            sleep(1);
        }
    }

    public function sendAudioWithEvolution($schedule){
        $instance = $schedule->instance->name;
        $numbers = explode(',', $schedule->group_id);
        $audio = $schedule->audio_path;
        $mention = !!$schedule->mention;
        foreach($numbers as $number){
            $number = trim($number);
            $this->sendAudio($instance, $number, $audio, mention:$mention);
            sleep(1);
        }
        $this->sendMessageWithEvolution($schedule);
    }

    public function sendMessageWithEvolution($schedule){
        $instance = $schedule->instance->name;
        $numbers = explode(',', $schedule->group_id);
        $mention = !!$schedule->mention;
        if(isset($schedule->link)){
            $message = str_replace('{{link}}', $schedule->link->url, $schedule->text);
        }else{
            $message = $schedule->text;
        }
        foreach($numbers as $number){
            $number = trim($number);
            $this->sendMessage($instance, $number, $message, mention:$mention);
            sleep(1);
        }
    }
    
    private function preparePhone($phone){
        $phone = preg_replace('/\D/', '', $phone);
    
        if (strlen($phone) <= 11) {
            $phone = '+55' . $phone;
        } else {
            $phone = '+' . $phone;
        }
    
        if (strlen($phone) == 13) {
            $phone = substr($phone, 0, 5) . '9' . substr($phone, 5);
        }
    
        return $phone;
    }
    
}
