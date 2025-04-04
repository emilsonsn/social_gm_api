<?php

namespace App\Trait;
use Dotenv\Dotenv;

use Illuminate\Support\Facades\Http;

Trait EvolutionTrait
{
    protected $apiKey;
    protected $baseUrl;

    public function prepareEvoCredentials()
    {
        if (file_exists(base_path('.env'))) {
            $dotenv = Dotenv::createImmutable(base_path());
            $dotenv->load();
        }

        $this->apiKey = env('EVO_API_KEY');
        $this->baseUrl = env('EVO_URL');
    }

    public function fetchInstances(){
        $getInstances = Http::withHeaders([
            'apiKey' => $this->apiKey,
        ])->get($this->baseUrl . '/instance/fetchInstances');

        $response = $getInstances->json();

        return $response;
    }

    public function createInstance($payload){
        $getInstances = Http::withHeaders([
            'apiKey' => $this->apiKey,
        ])->post($this->baseUrl . '/instance/create', $payload);

        $response = $getInstances->json();

        return $response;
    }

    public function connectInstance($instance){
        $getInstances = Http::withHeaders([
            'apiKey' => $this->apiKey,
        ])->get($this->baseUrl . "/instance/connect/$instance");

        $response = $getInstances->json();

        return $response;
    }

    public function logoutInstance($instance){
        $getInstances = Http::withHeaders([
            'apiKey' => $this->apiKey,
        ])->delete($this->baseUrl . "/instance/logout/$instance");

        $response = $getInstances->json();

        return $response;
    }

    public function deleteInstance($instance){
        $getInstances = Http::withHeaders([
            'apiKey' => $this->apiKey,
        ])->delete($this->baseUrl . "/instance/delete/$instance");

        $response = $getInstances->json();

        return $response;
    }
    
    public function fetchAllGroups($instance){
        $getInstances = Http::withHeaders([
            'apiKey' => $this->apiKey,
        ])
            ->timeout(120)
            ->get($this->baseUrl . "/group/fetchAllGroups/$instance?getParticipants=true");

        $response = $getInstances->json();

        return $response;
    }

    public function fetchAllParticipantsGroup($instance, $groupJid){
        $getInstances = Http::withHeaders([
            'apiKey' => $this->apiKey,
        ])->get($this->baseUrl . "/group/participants/$instance?groupJid=$groupJid");

        $response = $getInstances->json();

        return $response;
    }

    public function checkNumberTriggering(
        $baseUrl,
        $apiKey,
        $instance,
        $number,
    )
    {
        $response = Http::withHeaders([
            'apiKey' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post($baseUrl . "/chat/whatsappNumbers/{$instance}", [
            'numbers' => [
                $number
            ]
        ]);

        return $response->json();
    }

    public function sendMessage($instance, $number, $message, $mention)
    {
        $response = Http::withHeaders([
            'apiKey' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . "/message/sendText/{$instance}", [
            'number' => $number,
            'text' => $message,
            'mentionsEveryOne' => $mention,
            "linkPreview" => false
        ]);

        return $response->json();
    }

    public function sendAudio($instance, $number, $audio, $mention)
    {
        $response = Http::withHeaders([
            'apiKey' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . "/message/sendWhatsAppAudio/{$instance}", [
            'number' => $number,
            'audio' => $audio,
            'mentionsEveryOne' => $mention,
        ]);

        return $response->json();
    }

    public function sendMedia($instance, $number, $mediaType, $media, $caption, $mimeType = null, $fileName = null, $mention)
    {
        $response = Http::withHeaders([
            'apiKey' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . "/message/sendMedia/{$instance}", [
            'number' => $number,
            'mediatype' => $mediaType,
            'media' => $media,
            'caption' => $caption,
            'mimetype' => $mimeType,
            'fileName' => $fileName,
            "mentionsEveryOne" => $mention
        ]);

        return $response->json();
    }

    public function sendMediaTriggering(
        $baseUrl,
        $apiKey,
        $instance,
        $number,
        $media,
        $caption,
    )
    {
        $payload = [
            'number' => $number,
            'options' => [
                'delay'=>  1200,
                'presence'=> 'composing',
            ],
            'mediaMessage' => [
                'mediatype' => 'image',
                'caption' => $caption,
                'media' => $media,
            ],
        ];

        $response = Http::withHeaders([
            'apiKey' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post($baseUrl . "/message/sendMedia/{$instance}", $payload);

        return $response->json();
    }

            
}
