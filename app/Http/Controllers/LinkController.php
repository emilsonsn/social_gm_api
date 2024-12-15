<?php

namespace App\Http\Controllers;

use App\Services\Link\LinkService;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    private $linkService;

    public function __construct(LinkService $linkService) {
        $this->linkService = $linkService;
    }

    public function search(Request $request){
        $result = $this->linkService->search($request);

        return $result;
    }

    public function create(Request $request){
        $result = $this->linkService->create($request);

        if($result['status']) $result['message'] = "Links criado com sucesso";

        return $this->response($result);
    }
    public function update(Request $request, int $id){
        $result = $this->linkService->update($request, $id);

        if($result['status']) $result['message'] = "Links atualizado com sucesso";

        return $this->response($result);
    }

    public function delete(int $id){
        $result = $this->linkService->delete($id);

        if($result['status']) $result['message'] = "Links deletado com sucesso";

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
