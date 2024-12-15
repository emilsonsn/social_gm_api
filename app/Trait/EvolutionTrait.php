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
            
}
