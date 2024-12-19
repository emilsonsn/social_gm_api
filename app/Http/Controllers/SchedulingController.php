<?php

namespace App\Http\Controllers;

use App\Services\Scheduling\SchedulingService;
use Illuminate\Http\Request;

class SchedulingController extends Controller
{
    private $schedulingService;

    public function __construct(SchedulingService $schedulingService) {
        $this->schedulingService = $schedulingService;
    }

    public function search(Request $request){
        $result = $this->schedulingService->search($request);

        return $result;
    }

    public function create(Request $request){
        $result = $this->schedulingService->create($request);

        if($result['status']) {
            $result['message'] = "Agendamento criado com sucesso";
            if($result['data']['status'] == 'Model'){
                $result['message'] = "Modelo criado com sucesso";
            }
        }

        return $this->response($result);
    }

    public function copy(int $id){
        $result = $this->schedulingService->copy($id);

        if($result['status']) $result['message'] = "Agendamento copiado com sucesso";

        return $this->response($result);
    }

    public function update(Request $request, int $id){
        $result = $this->schedulingService->update($request, $id);

        if($result['status']) {
            $result['message'] = "Agendamento atualizado com sucesso";
            if($result['data']['status'] == 'Model'){
                $result['message'] = "Modelo atualizado com sucesso";
            }
        }

        return $this->response($result);
    }

    public function delete($id){
        $result = $this->schedulingService->delete($id);

        if($result['status']) $result['message'] = "Agendamento excluído com sucesso";

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
