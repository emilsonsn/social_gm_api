<?php

namespace App\Http\Controllers;

use App\Services\ContactList\ContactListService;
use Illuminate\Http\Request;

class ContactListController extends Controller
{
    private $contactListService;

    public function __construct(ContactListService $contactListService) {
        $this->contactListService = $contactListService;
    }

    public function search(Request $request){
        $result = $this->contactListService->search($request);

        return $result;
    }

    public function import(Request $request){
        $result = $this->contactListService->import($request);

        if($result['status']) $result['message'] = "Contatos importados com sucesso";

        return $this->response($result);
    }

    public function delete(int $id){
        $result = $this->contactListService->delete($id);

        if($result['status']) $result['message'] = "Lista de contatos deletada com sucesso";

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
