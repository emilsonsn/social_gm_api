<?php

namespace App\Services\Routine;

use App\Models\Scheduling;
use App\Trait\EvolutionTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class RoutineService
{

    use EvolutionTrait; 

    public function handleMessage(){

        $schedules = Scheduling::where('status', 'Waiting')
            ->whereRaw("DATE_FORMAT(datetime, '%Y-%m-%d %H:%i') = ?", [Carbon::now()->format('Y-m-d H:i')])
            ->get();

        foreach($schedules as $schedule){
            try{
                $this->prepareEvoCredentials();
                switch($schedule->midia){
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
                
                $schedule->status = 'Sent';
                $schedule->save();
            }
            catch(Exception $error){
                Log::error($error->getMessage());
            }
        }            
    }

    public function sendMideaWithEvolution($schedule, $type)
    {
        $instance = $schedule->instance->name;
        $number = $schedule->group_id;
        $mediaType = $type;
        $media = $type === 'video' ? $schedule->video_path : $schedule->image_path;
        $mimeType = mime_content_type($media);
        $fileExtension = pathinfo($media, PATHINFO_EXTENSION);
        $fileName = "midea_automation.{$fileExtension}";
        $caption = str_replace('{{link}}', $schedule->link->url, $schedule->text);
        $mention = $schedule->mention ?? false;
        $this->sendMedia($instance, $number, $mediaType, $media, $caption, $mimeType, $fileName, mention: $mention);
    }
    

    public function sendAudioWithEvolution($schedule){
        $instance = $schedule->instance->name;
        $number = $schedule->group_id;
        $audio = $schedule->audio_path;
        $mention = $schedule->mention ?? false;
        $this->sendAudio($instance, $number, $audio, mention:$mention);
        $this->sendMessageWithEvolution($schedule);
    }

    public function sendMessageWithEvolution($schedule){
        $instance = $schedule->instance->name;
        $number = $schedule->group_id;
        $message = $schedule->text;
        $mention = $schedule->mention ?? false;     
        $this->sendMessage($instance, $number, $message, mention:$mention);
    }
}
