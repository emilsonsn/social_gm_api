<?php

namespace App\Http\Controllers;

use App\Services\Triggering\TriggeringService;
use Illuminate\Http\Request;

class TriggeringController extends Controller
{
    private $triggeringService;

    public function __construct(TriggeringService $triggeringService) {
        $this->triggeringService = $triggeringService;
    }

    public function search(Request $request){
        $result = $this->triggeringService->search($request);

        return $result;
    }

    public function create(Request $request){
        $result = $this->triggeringService->create($request);

        if($result['status']) $result['message'] = "Disparo criado com sucesso";

        return $this->response($result);
    }
    public function update(Request $request, int $id){
        $result = $this->triggeringService->update($request, $id);

        if($result['status']) $result['message'] = "Disparo atualizado com sucesso";

        return $this->response($result);
    }

    public function delete(int $id){
        $result = $this->triggeringService->delete($id);

        if($result['status']) $result['message'] = "Disparo deletado com sucesso";

        return $this->response($result);
    }

    private function response($result){
        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'] ?? null,
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null
        ], $result['statusCode'] ?? 200);
    }
}
