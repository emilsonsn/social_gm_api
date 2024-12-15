<?php

namespace App\Services\Scheduling;

use App\Models\Scheduling;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SchedulingService
{
    public function search($request)
    {
        try{

            $scheduling_id = $request->scheduling;

            $schedulings = Scheduling::where('scheduling_id', $scheduling_id);

            if($request->filled('date')){
                $schedulings->whereDate('datetime', $request->date);
            }

            $schedulings = $schedulings->paginate(10);

            return [
                'status' => true,
                'data' => $schedulings
            ];

        } catch(Exception $error){
            return [
                'status' => false,
                'message' => $error->getMessage(),
                'statusCode' => $error->getCode() ?? 400
            ];  
        }
    }

    public function create($request)
    {
        try{
            $rules = [
                'description' => ['required', 'string', 'max:255'],
                'instance_id' => ['required', 'integer'],
                'group_id' => ['required', 'string'],
                'text' => ['nullable', 'string'],
                'video' => ['nullable', 'file', 'mime:mp4'],
                'audio' => ['nullable', 'file', 'mime:mp3'],
                'image' => ['nullable', 'file', 'mime:png,jpeg,webp'],
                'status' => ['nullable', 'string', 'in:Model,Waiting,Sent,Inactive'],
                'datetime' => ['nullable', 'string'],
                'user_id' => ['nullable', 'string'],
            ];                                    

            $requestData = $request->all();

            $validator = Validator::make($requestData, $rules);

            if($validator->fails()){
                throw new Exception($validator->errors(), 400);
            }

            if ($request->hasFile('image')) {
                $image_path = $request->file('image')->store('public/image');
                $requestData['image_path'] = str_replace('public/', '', $image_path);
            }
            
            if ($request->hasFile('audio')) {
                $audioPath = $request->file('audio')->store('public/audio');
                $requestData['audio_path'] = str_replace('public/', '', $audioPath);
            }
            
            if ($request->hasFile('video')) {
                $videoPath = $request->file('video')->store('public/video');
                $requestData['video_path'] = str_replace('public/', '', $videoPath);
            }

            if(!isset($requestData['user_id'])) $requestData['user_id'] = Auth::user()->id;

            $scheduling = Scheduling::create($requestData);
                
            return [
                'status' => true,
                'data' => $scheduling
            ];

        } catch(Exception $error){
            return [
                'status' => false,
                'message' => $error->getMessage(),
                'statusCode' => $error->getCode() ?? 400
            ];  
        }
    }

    public function update($request, $id)
    {
        try{
            $rules = [
                'description' => ['required', 'string', 'max:255'],
                'instance_id' => ['required', 'integer'],
                'group_id' => ['required', 'string'],
                'text' => ['nullable', 'string'],
                'video' => ['nullable', 'file', 'mime:mp4'],
                'audio' => ['nullable', 'file', 'mime:mp3'],
                'image' => ['nullable', 'file', 'mime:png,jpeg,webp'],
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

            if ($request->hasFile('image')) {
                $image_path = $request->file('image')->store('public/image');
                $requestData['image_path'] = str_replace('public/', '', $image_path);
            }

            if ($request->hasFile('audio')) {
                $audioPath = $request->file('audio')->store('public/audio');
                $requestData['audio_path'] = str_replace('public/', '', $audioPath);
            }
            
            if ($request->hasFile('video')) {
                $videoPath = $request->file('video')->store('public/video');
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
                'statusCode' => $error->getCode() ?? 400
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
                'statusCode' => $error->getCode() ?? 400
            ];  
        }
    }
}
