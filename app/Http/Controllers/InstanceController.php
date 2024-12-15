<?php

namespace App\Http\Controllers;

use App\Services\Instance\InstanceService;
use Illuminate\Http\Request;

class InstanceController extends Controller
{
    private $instanceService;

    public function __construct(InstanceService $instanceService) {
        $this->instanceService = $instanceService;
    }

    public function search(Request $request){
        $result = $this->instanceService->search($request);

        return $this->response($result);
    }

    public function connect($instanceName){
        $result = $this->instanceService->connect($instanceName);

        return $this->response($result);
    }

    public function groups($instanceName){
        $result = $this->instanceService->groups($instanceName);

        return $this->response($result);
    }

    public function create(Request $request){
        $result = $this->instanceService->create($request);

        if($result['status']) $result['message'] = "Instância criada com sucesso";

        return $this->response($result);
    }

    public function delete($id){
        $result = $this->instanceService->delete($id);

        if($result['status']) $result['message'] = "Instância deletada com sucesso";

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
