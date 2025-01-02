<?php

namespace App\Services\Triggering;

use App\Models\Triggering;
use App\Models\TriggeringMessage;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TriggeringService
{
    public function search($request)
    {
        try {
            $take = $request->take ?? 10;
        
            $auth = Auth::user();
        
            $triggerings = Triggering::with('messages', 'list', 'user')
                ->where('user_id', $auth->id)
                ->orderBy('id', 'desc')
                ->paginate($take);
        
            // Iterar sobre cada triggering para incluir os contatos
            $triggerings->getCollection()->each(function ($triggering) {
                $contactList = $triggering->list;
    
                $triggering->pending_contacts = $contactList->contacts()->where('is_whatsapp', 'Pending')->count();
                
                $triggering->notfound_contacts = $contactList->contacts()->where('is_whatsapp', 'NotFound')->count();
    
                $triggering->whatsapp_contacts = $contactList->contacts()->where('is_whatsapp', 'Whatsapp')->count();

                $triggering->total_contacts = $contactList->contacts()->count();
                
                $pendingContacts = $triggering->pending_contacts;
                $interval = $triggering->interval ?? 0;
    
                $triggering->remaining_time = $interval * $pendingContacts;
            });
        
            return $triggerings;
        } catch (Exception $error) {
            return [
                'status' => false,
                'message' => $error->getMessage(),
                'statusCode' => 400,
            ];
        }
    }

    public function create($request)
    {
        try{
            $rules = [
                'description' => ['required', 'string'],
                'contact_list_id' => ['required', 'integer', 'exists:contact_lists,id'],
                'evo_url' => ['required', 'string'],
                'evo_key' => ['required', 'string'],
                'evo_instance' => ['required', 'string'],
                'interval' => ['required', 'string'],
                'file' => ['nullable', 'file'],
                'messages' => ['required', 'array'],
                'messages.*' => ['string'],
            ];                                    

            $requestData = $request->all();

            $validator = Validator::make($requestData, $rules);

            if($validator->fails()){
                throw new Exception($validator->errors(), 400);
            }

            $requestData['user_id'] = Auth::user()->id;

            $path = $request->file('file')->store('public/images');
            $requestData['path'] = str_replace('public/', '', $path);

            $triggering = Triggering::create($requestData);

            foreach($request->messages as $message){
                $triggering['messages'][] = TriggeringMessage::create([
                    'message' => $message,
                    'triggering_id' => $triggering->id,
                ]);                
            }
                
            return [
                'status' => true,
                'data' => $triggering
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
                'description' => ['required', 'string'],
                'contact_list_id' => ['required', 'integer', 'exists:contact_lists,id'],
                'evo_url' => ['required', 'string'],
                'evo_key' => ['required', 'string'],                
                'evo_instance' => ['required', 'string'],
                'interval' => ['required', 'string'],
                'file' => ['nullable', 'file'],
                'messages' => ['required', 'array'],
                'messages.*' => ['string'],
            ];   

            $requestData = $request->all();

            $validator = Validator::make($requestData, $rules);

            if($validator->fails()){
                throw new Exception($validator->errors(), 400);
            }

            $triggeringToUpdate = Triggering::find($id);

            if(!isset($triggeringToUpdate)){
                throw new Exception("Disparo não encontrado", 400);
            }

            $requestData['path'] = '';

            if($request->hasFile('file')){
                $path = $request->file('file')->store('public/images');
                $requestData['path'] = str_replace('public/', '', $path);
            }

            $triggeringToUpdate->update($requestData);

            foreach($request->messages as $message){
                $triggering['messages'][] = TriggeringMessage::updateOrCreate([
                    'message' => $message,
                    'triggering_id' => $triggeringToUpdate->id,
                ]);                
            }
                
            return [
                'status' => true,
                'data' => $triggeringToUpdate
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
            
            $triggering = Triggering::find($id);

            if(!isset($triggering)){
                throw new Exception('Disparo não encontrado', 400);
            }

            $triggeringId = $triggering->id;

            $triggering->delete();

            return [
                'status' => true,
                'data' => $triggeringId
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
