<?php

namespace App\Services\Automation;

use App\Models\Instance;
use App\Models\Automation;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AutomationService
{
    public function search($request)
    {
        try{
            $instance_id = $request->instance_id;

            $automations = Automation::where('instance_id', $instance_id);

            $automations = $automations->paginate(10);

            return $automations;

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
                'instance_id' => ['required', 'string'],
                'group_id' => ['required', 'string'],
                'farewell_message' => ['nullable', 'string'],
                'welcome_message' => ['nullable', 'string'],
            ];                                    

            $requestData = $request->all();

            $validator = Validator::make($requestData, $rules);

            if($validator->fails()){
                throw new Exception($validator->errors(), 400);
            }

            $automation = Automation::create($requestData);
                
            return [
                'status' => true,
                'data' => $automation
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
                'instance_id' => ['required', 'string'],
                'group_id' => ['required', 'string'],
                'welcome_message' => ['nullable', 'string'],
                'farewell_message' => ['nullable', 'string'],
            ];  

            $requestData = $request->all();

            $validator = Validator::make($requestData, $rules);

            if($validator->fails()){
                throw new Exception($validator->errors(), 400);
            }

            $automationToUpdate = Automation::find($id);

            if(!isset($automationToUpdate)){
                throw new Exception("Automação não encontrada", 400);
            }
            
            $automationToUpdate->update($requestData);
                
            return [
                'status' => true,
                'data' => $automationToUpdate
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
            
            $automation = Automation::find($id);

            if(!isset($automation)){
                throw new Exception('Automação não encontrada', 400);
            }

            $automationId = $automation->id;

            $automation->delete();

            return [
                'status' => true,
                'data' => $automationId
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
