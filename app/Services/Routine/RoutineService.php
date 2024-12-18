<?php

namespace App\Services\Routine;

use App\Models\MessageSendingLog;
use App\Models\Scheduling;
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
}
