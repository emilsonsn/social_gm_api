<?php

namespace App\Services\Scheduling;

use App\Models\Instance;
use App\Models\Scheduling;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SchedulingService
{
    public function search($request)
    {
        try{
            $instance_id = $request->instance_id;

            $instance = Instance::where('external_id', $instance_id)
                ->orWhere('id', $instance_id)
                ->first();

            $schedulings = Scheduling::where('instance_id', $instance->id);

            if($request->filled('description')){
                $schedulings->where('description', 'LIKE', "%$request->description%");
            }

            if($request->filled('status')){
                $status = explode(',',$request->status);
                $schedulings->whereIn('status', $status);
            }

            if($request->filled('date')){
                $schedulings->whereDate('datetime', $request->date);
            }else{
                $schedulings->whereDate('datetime', Carbon::now());
            }

            $schedulings = $schedulings->paginate(10);

            return $schedulings;

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
            $request['mention'] = $request['mention'] === 'true'? true : false;

            $rules = [
                'description' => ['required', 'string', 'max:255'],
                'midia' => ['nullable', 'string', 'in:audio,video,imagem,text'],
                'mention' => ['nullable', 'boolean'],
                'instance_id' => ['required'],
                'group_id' => ['required', 'string'],
                'link_id' => ['nullable', 'integer'],
                'group_name' => ['required', 'string'],
                'text' => ['nullable', 'string'],
                'status' => ['nullable', 'string', 'in:Model,Waiting,Sent,Inactive'],
                'datetime' => ['nullable', 'string'],
                'user_id' => ['nullable', 'integer'],
            ];                                    

            $requestData = $request->all();

            $validator = Validator::make($requestData, $rules);

            if($validator->fails()){
                throw new Exception($validator->errors(), 400);
            }

            if ($request->hasFile('image_path')) {
                $image_path = $request->file('image_path')->store('public/image');
                $requestData['image_path'] = str_replace('public/', '', $image_path);
            }
            
            if ($request->hasFile('audio_path')) {
                $audioPath = $request->file('audio_path')->store('public/audio');
                $requestData['audio_path'] = str_replace('public/', '', $audioPath);
            }
            
            if ($request->hasFile('video_path')) {
                $videoPath = $request->file('video_path')->store('public/video');
                $requestData['video_path'] = str_replace('public/', '', $videoPath);
            }

            if(!isset($requestData['user_id'])) $requestData['user_id'] = Auth::user()->id;

            $instance = Instance::where('external_id', $requestData['instance_id'])
                ->orWhere('id', $requestData['instance_id'])
                ->first();
            
            if(!isset($instance)) throw new Exception('Instância não encontrada');

            $requestData['instance_id'] = $instance->id;

            $scheduling = Scheduling::create($requestData);
                
            return [
                'status' => true,
                'data' => $scheduling
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
            $request['mention'] = $request['mention'] === 'true'? true : false;

            $rules = [
                'description' => ['required', 'string', 'max:255'],
                'midia' => ['nullable', 'string', 'in:audio,video,imagem,text'],
                'mention' => ['nullable', 'boolean'],
                'instance_id' => ['required'],
                'group_id' => ['required', 'string'],
                'link_id' => ['nullable', 'integer'],
                'group_name' => ['required', 'string'],
                'text' => ['nullable', 'string'],
                'status' => ['nullable', 'string', 'in:Model,Waiting,Sent,Inactive'],
                'datetime' => ['nullable', 'string'],
            ]; 

            $requestData = $request->all();

            $validator = Validator::make($requestData, $rules);

            if($validator->fails()){
                throw new Exception($validator->errors(), 400);
            }

            $schedulingToUpdate = Scheduling::find($id);

            if(!isset($schedulingToUpdate)){
                throw new Exception("Agendamento não encontrado", 400);
            }

            if ($request->hasFile('image_path')) {
                $image_path = $request->file('image_path')->store('public/image');
                $requestData['image_path'] = str_replace('public/', '', $image_path);
            }
            
            if ($request->hasFile('audio_path')) {
                $audioPath = $request->file('audio_path')->store('public/audio');
                $requestData['audio_path'] = str_replace('public/', '', $audioPath);
            }
            
            if ($request->hasFile('video_path')) {
                $videoPath = $request->file('video_path')->store('public/video');
                $requestData['video_path'] = str_replace('public/', '', $videoPath);
            }
            
            $schedulingToUpdate->update($requestData);
                
            return [
                'status' => true,
                'data' => $schedulingToUpdate
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
            
            $scheduling = Scheduling::find($id);

            if(!isset($scheduling)){
                throw new Exception('Não foi possível encontrar agendamento');
            }

            $schedulingId = $scheduling->id;

            $scheduling->delete();

            return [
                'status' => true,
                'data' => $schedulingId
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
