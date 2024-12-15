<?php

namespace App\Http\Controllers;

use App\Services\Automation\AutomationService;
use Illuminate\Http\Request;

class AutomationController extends Controller
{
    private $automationService;

    public function __construct(AutomationService $automationService) {
        $this->automationService = $automationService;
    }

    public function search(Request $request){
        $result = $this->automationService->search($request);

        return $result;
    }

    public function create(Request $request){
        $result = $this->automationService->create($request);

        if($result['status']) $result['message'] = "Automação criada com sucesso";

        return $this->response($result);
    }
    public function update(Request $request, int $id){
        $result = $this->automationService->update($request, $id);

        if($result['status']) $result['message'] = "Automação atualizada com sucesso";

        return $this->response($result);
    }

    public function delete(int $id){
        $result = $this->automationService->delete($id);

        if($result['status']) $result['message'] = "Automação deletada com sucesso";

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
