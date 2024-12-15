<?php

namespace App\Services\Instance;

use App\Enums\UserRoleEnum;
use App\Models\Instance;
use App\Models\User;
use App\Trait\EvolutionTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InstanceService
{

    use EvolutionTrait;

    public function search($request)
    {
        try{
            $auth = Auth::user();
            $this->prepareEvoCredentials();
            $instances = $this->fetchInstances();

            $this->importInstances($instances);

            if(!isset($instances) || !count($instances)){
                throw new Exception('Nenhuma instancia encontrada');
            }

            if($auth->role !== UserRoleEnum::Admin->value){
                $user_id = $auth->id;
    
                $userInstances =  Instance::where('user_id', $user_id)
                    ->pluck('name')
                    ->toArray();
                
                $instancesResult = [];
                foreach($instances as $instance){
                    if(in_array($instance['name'], $userInstances)){
                        $instancesResult[] = $instance;
                    }
                }
            }else{
                $instancesResult = $instances; 
            }

            return [
                'status' => true,
                'data' => $instancesResult
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
                'name' => ['required', 'string']
            ];

            $requestData = $request->all();

            $validator = Validator::make($requestData, $rules);

            if($validator->fails()){
                throw new Exception($validator->errors(), 400);
            }

            $payload = [
                'instanceName' => $requestData['name'],
                'qrcode' => true,
                'integration' => 'WHATSAPP-BAILEYS'
            ];

            $this->prepareEvoCredentials();
            $instance = $this->createInstance($payload);

            if(!isset($instance['instance'])){
                throw new Exception('Erro ao criar instância');
            }

            $instance['instance'] = Instance::create(
                [
                    'external_id' => $instance['instance']['instanceId'],
                    'api_key' => $instance['hash'],
                    'name' => $instance['instance']['instanceName'],
                    'user_id' => Auth::user()->id,
                ]
            );
                
            return [
                'status' => true,
                'data' => $instance
            ];

        } catch(Exception $error){
            return [
                'status' => false,
                'message' => $error->getMessage(),
                'statusCode' => $error->getCode() ?? 400
            ];  
        }
    }

    public function connect($instanceName){
        try{

            $instance = Instance::where('name', $instanceName)
                ->first();

            if(!isset($instance)){
                throw new Exception('Instância não encontrada', 400);
            }        

            $this->prepareEvoCredentials();
            $instanceConnect = $this->connectInstance($instanceName);

            if(!isset($instanceConnect['base64'])){
                throw new Exception('Erro ao gerar qrcode da instância');
            }
                
            return [
                'status' => true,
                'data' => $instanceConnect
            ];

        } catch(Exception $error){
            return [
                'status' => false,
                'message' => $error->getMessage(),
                'statusCode' => $error->getCode() ?? 400
            ];  
        }
    }

    public function groups($id){
        try{

            $instance = Instance::where('external_id', $id)
                ->first();

            if(!isset($instance)){
                throw new Exception('Instância não encontrada', 400);
            }        

            $this->prepareEvoCredentials();
            $groups = $this->fetchAllGroups($instance->name);

            if(!isset($groups) || !count($groups)){
                throw new Exception('Erro ao gerar qrcode da instância');
            }
                
            return [
                'status' => true,
                'data' => $groups
            ];

        } catch(Exception $error){
            return [
                'status' => false,
                'message' => $error->getMessage(),
                'statusCode' => $error->getCode() ?? 400
            ];  
        }
    }

    private function importInstances($instances){
        $user = User::where('role', 'Admin')->first();
        foreach($instances as $instance){
            $instance_id = $instance['id'];

            if(Instance::where('external_id', $instance_id)->count()) continue;

            Instance::create(
                [
                    'external_id' => $instance_id,
                    'api_key' => $instance['token'],
                    'name' => $instance['name'],
                    'user_id' => $user->id,
                ]
            );
        }
    }

    public function delete($id){
        try{

            $instance = Instance::where('id', $id)
                ->orWhere('external_id', $id)
                ->first();

            if(!isset($instance)){
                throw new Exception('Instância não encontrada', 400);
            }        

            $this->prepareEvoCredentials();
            $this->logoutInstance($instance->name);
            $this->deleteInstance($instance->name);

            $instanceName = $instance->name;
            $instance->delete();
                
            return [
                'status' => true,
                'data' => $instanceName
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
