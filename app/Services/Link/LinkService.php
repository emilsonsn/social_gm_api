<?php

namespace App\Services\Link;

use App\Enums\UserRoleEnum;
use App\Models\Instance;
use App\Models\Link;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LinkService
{
    public function search($request)
    {
        try{            
            $links = Link::orderBy('id', 'desc');

            $auth = Auth::user();

            if($auth->role !== UserRoleEnum::Admin->value){
                $links->where('user_id', $auth->id);
            }

            $links = $links->paginate(10);

            return $links;

        } catch(Exception $error){
            return [
                'status' => false,
                'message' => $error->getMessage(),
                'statusCode' => 400
            ];  
        }
    }

    public function create($request)
    {
        try{
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'url' => ['required', 'string', 'max:255'],
            ];                                    

            $requestData = $request->all();

            $validator = Validator::make($requestData, $rules);

            if($validator->fails()){
                throw new Exception($validator->errors(), 400);
            }

            $requestData['user_id'] = Auth::user()->id;

            $link = Link::create($requestData);
                
            return [
                'status' => true,
                'data' => $link
            ];

        } catch(Exception $error){
            return [
                'status' => false,
                'message' => $error->getMessage(),
                'statusCode' => 400
            ];  
        }
    }

    public function update($request, $id)
    {
        try{
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'url' => ['required', 'string', 'max:255'],
                'user_id' => ['required', 'integer'],
            ];   

            $requestData = $request->all();

            $validator = Validator::make($requestData, $rules);

            if($validator->fails()){
                throw new Exception($validator->errors(), 400);
            }

            $linkToUpdate = Link::find($id);

            if(!isset($linkToUpdate)){
                throw new Exception("Link não encontrado", 400);
            }

            $linkToUpdate->update($requestData);
                
            return [
                'status' => true,
                'data' => $linkToUpdate
            ];

        } catch(Exception $error){
            return [
                'status' => false,
                'message' => $error->getMessage(),
                'statusCode' => 400
            ];  
        }
    }

    public function delete($id)
    {
        try{
            
            $link = Link::find($id);

            if(!isset($link)){
                throw new Exception('Link não encontrado', 400);
            }

            $linkId = $link->id;

            $link->delete();

            return [
                'status' => true,
                'data' => $linkId
            ];

        } catch(Exception $error){
            return [
                'status' => false,
                'message' => $error->getMessage(),
                'statusCode' => 400
            ];  
        }
    }
}
