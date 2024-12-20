<?php

namespace App\Http\Controllers;

use App\Services\PrizeDraw\PrizeDrawService;
use Illuminate\Http\Request;

class PrizeDrawController extends Controller
{
    private $drizeDrawService;

    public function __construct(PrizeDrawService $drizeDrawService) {
        $this->drizeDrawService = $drizeDrawService;
    }


    public function search(Request $request){
        $result = $this->drizeDrawService->search($request);

        return $result;
    }

    public function create(Request $request){
        $result = $this->drizeDrawService->create($request);

        if($result['status']) $result['message'] = "Sorteio criado com sucesso";

        return $this->response($result);
    }

    public function addDrawn(Request $request){
        $result = $this->drizeDrawService->create($request);

        if($result['status']) $result['message'] = "Sorteado adicionado com sucesso";

        return $this->response($result);
    }

    public function copy(int $id){
        $result = $this->drizeDrawService->copy($id);

        if($result['status']) $result['message'] = "Agendamento copiado com sucesso";

        return $this->response($result);
    }

    public function update(Request $request, int $id){
        $result = $this->drizeDrawService->update($request, $id);

        if($result['status']) {
            $result['message'] = "Agendamento atualizado com sucesso";
            if($result['data']['status'] == 'Model'){
                $result['message'] = "Modelo atualizado com sucesso";
            }
        }

        return $this->response($result);
    }

    public function delete($id){
        $result = $this->drizeDrawService->delete($id);

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
