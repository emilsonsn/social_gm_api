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
            $take = $request->take ?? 10;
            $orderField = $request->orderField ?? 'datetime';
            $order = $request->order ?? 'desc';
            $instance_id = $request->instance_id;

            $instance = Instance::where(function($query) use($instance_id){
                $query->where('id', $instance_id)
                ->orWhere('external_id', $instance_id);             
            })->first();

            $schedulings = Scheduling::orderBy($orderField, $order)
                ->orderBy('id', 'desc');

            $schedulings->where(function($query) use($instance){
                $query->where('instance_id', $instance->id)
                    ->orWhere('instance_id', $instance->external_id);
            });
              
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
                if ('Model' != $request->status){
                    $schedulings->whereDate('datetime', Carbon::now());
                } 
            }

            $schedulings = $schedulings->paginate($take);

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
            $request['mention'] = isset($request['mention']) ? !!(int)$request['mention'] : 0;

            $rules = [
                'description' => ['required', 'string', 'max:255'],
                'midia' => ['nullable', 'string', 'in:audio,video,imagem,text'],
                'mention' => ['nullable', 'boolean'],
                'instance_id' => ['required'],
                'group_id' => ['nullable', 'string'],
                'link_id' => ['nullable', 'integer'],
                'group_name' => ['nullable', 'string'],
                'text' => ['nullable', 'string'],
                'status' => ['nullable', 'string', 'in:Model,Waiting,Sent,Copy,Inactive'],
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
            }else if(is_string($request->image_path)){                
                $baseAndPath = explode('storage/',$request->image_path);
                if(count($baseAndPath) > 1) $requestData['image_path'] = $baseAndPath[1];
            }
            
            if ($request->hasFile('audio_path')) {
                $audioPath = $request->file('audio_path')->store('public/audio');
                $requestData['audio_path'] = str_replace('public/', '', $audioPath);
            }else if(is_string($request->audio_path)){                
                $baseAndPath = explode('storage/',$request->audio_path);
                if(count($baseAndPath) > 1) $requestData['audio_path'] = $baseAndPath[1];
            }
            
            if ($request->hasFile('video_path')) {
                $videoPath = $request->file('video_path')->store('public/video');
                $requestData['video_path'] = str_replace('public/', '', $videoPath);
            }else if(is_string($request->video_path)){                
                $baseAndPath = explode('storage/',$request->video_path);
                if(count($baseAndPath) > 1) $requestData['video_path'] = $baseAndPath[1];
            }

            if(!isset($requestData['user_id'])) $requestData['user_id'] = Auth::user()->id;

            if (is_numeric($requestData['instance_id'])) {
                $instance = Instance::where('id', $requestData['instance_id'])
                    ->first();
            } else {
                $instance = Instance::where('external_id', $requestData['instance_id'])
                    ->first();
            }
            
            if(!isset($instance)) throw new Exception('Agendamento não encontrada');

            $requestData['instance_id'] = $instance->id;

            $requestData['status'] = $requestData['status'] ?? 'Waiting';
            if($requestData['status'] === 'Copy') $requestData['status'] = 'Waiting';

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

    public function copy($id){
        try{
            $scheduling = Scheduling::find($id);

            if(!isset($scheduling)) {
                throw new Exception('Agendamento não encontrado');
            }

            $duplicateScheduling = $scheduling->replicate();
            $duplicateScheduling->status = 'Copy';
            $duplicateScheduling->save();

            return [
                'status' => true,
                'data' => $duplicateScheduling
            ];
        }catch(Exception $error){
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
            $request['mention'] = isset($request['mention']) ? !!(int)$request['mention'] : 0;

            $rules = [
                'description' => ['required', 'string', 'max:255'],
                'midia' => ['nullable', 'string', 'in:audio,video,imagem,text'],
                'mention' => ['nullable', 'boolean'],
                'instance_id' => ['required'],
                'group_id' => ['nullable', 'string'],
                'link_id' => ['nullable', 'integer'],
                'group_name' => ['nullable', 'string'],
                'text' => ['nullable', 'string'],
                'status' => ['nullable', 'string', 'in:Model,Waiting,Sent,Copy,Inactive'],
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
            }else if(is_string($request->image_path)){                
                $baseAndPath = explode('storage/',$request->image_path);
                if(count($baseAndPath) > 1) $requestData['image_path'] = $baseAndPath[1];
            }
            
            if ($request->hasFile('audio_path')) {
                $audioPath = $request->file('audio_path')->store('public/audio');
                $requestData['audio_path'] = str_replace('public/', '', $audioPath);
            }else if(is_string($request->audio_path)){                
                $baseAndPath = explode('storage/',$request->audio_path);
                if(count($baseAndPath) > 1) $requestData['audio_path'] = $baseAndPath[1];
            }
            
            if ($request->hasFile('video_path')) {
                $videoPath = $request->file('video_path')->store('public/video');
                $requestData['video_path'] = str_replace('public/', '', $videoPath);
            }else if(is_string($request->video_path)){                
                $baseAndPath = explode('storage/',$request->video_path);
                if(count($baseAndPath) > 1) $requestData['video_path'] = $baseAndPath[1];
            }

            if(!isset($requestData['user_id'])) $requestData['user_id'] = Auth::user()->id;

            if (is_numeric($requestData['instance_id'])) {
                $instance = Instance::where('id', $requestData['instance_id'])
                    ->first();
            } else {
                $instance = Instance::where('external_id', $requestData['instance_id'])
                    ->first();
            }

            if(!isset($instance)) throw new Exception('Agendamento não encontrada');

            $requestData['instance_id'] = $instance->id;
            
            $requestData['status'] = $requestData['status'] ?? 'Waiting';

            if($requestData['status'] === 'Copy') $requestData['status'] = 'Waiting';
            
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
                throw new Exception('Agendamento não encontrado');
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
