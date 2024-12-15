<?php

namespace App\Trait;

use Illuminate\Support\Facades\Http;

Trait EvolutionTrait
{
    protected $apiKey;
    protected $baseUrl;

    public function prepareEvoCredentials()
    {
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

    public function fetchAllGroups($instance){
        $getInstances = Http::withHeaders([
            'apiKey' => $this->apiKey,
        ])->get($this->baseUrl . "/group/fetchAllGroups/$instance?getParticipants=false");

        $response = $getInstances->json();

        return $response;
    }

    public function sendMessage($instance, $number, $message)
    {
        $response = Http::withHeaders([
            'apiKey' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . "/message/sendText/{$instance}", [
            'number' => $number,
            'text' => $message,
        ]);

        return $response->json();
    }

    public function sendAudio($instance, $number, $audio, $delay = null, $encoding = null, $quoted = null, $mentionsEveryOne = null, $mentioned = null)
    {
        $response = Http::withHeaders([
            'apiKey' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . "/message/sendWhatsAppAudio/{$instance}", [
            'number' => $number,
            'audio' => $audio,
            'delay' => $delay,
            'encoding' => $encoding,
            'quoted' => $quoted,
            'mentionsEveryOne' => $mentionsEveryOne,
            'mentioned' => $mentioned,
        ]);

        return $response->json();
    }

    public function sendMedia($instance, $number, $mediaType, $media, $caption, $mimeType = null, $fileName = null)
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
        ]);

        return $response->json();
    }

            
}
