<?php

namespace App\Http\Controllers;

use App\Services\Routine\WebhookService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{

    private $webhookService;

    public function __construct(WebhookService $webhookService = null) {
        $this->webhookService = $webhookService;
    }
    public function handle(Request $request){
        $this->webhookService->handleMessage($request);
    }
}


