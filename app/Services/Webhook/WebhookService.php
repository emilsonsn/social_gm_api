<?php

namespace App\Services\Routine;

use App\Models\Automation;
use App\Models\Instance;
use App\Trait\EvolutionTrait;

class WebhookService
{

    use EvolutionTrait; 

    public function handleMessage($request){
        if($request['event'] === 'group-participants.update'){
            switch($request['data']['action']){
                case 'add':
                    $this->addParticipant($request);
                    break;
                case 'remove':
                    $this->removeParticipant($request);
                    break;
            }
        }       
    }

    private function addParticipant($data){
        $instance = Instance::where('name', $data['instance'])->first();

        if(!isset($instance)) return;

        $automation = Automation::where('instance_id', $instance->external_id)
            ->whereNotNull('welcome_message')
            ->first();

        if(!isset($automation)) return;

        $instanceName = $instance->name;
        $message = $automation->welcome_message;
        $number = $data['data']['participants'][0];
        $this->sendMessage($instanceName, $number, $message);
    }

    private function removeParticipant($data){
        $instance = Instance::where('name', $data['instance'])->first();

        if(!isset($instance)) return;

        $automation = Automation::where('instance_id', $instance->external_id)
            ->whereNotNull('farewell_message')
            ->first();

        if(!isset($automation)) return;

        $instanceName = $instance->name;
        $message = $automation->farewell_message;
        $number = $data['data']['participants'][0];
        $this->sendMessage($instanceName, $number, $message);
    }
}
