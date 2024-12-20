<?php

namespace App\Services\PrizeDraw;

use App\Models\Instance;
use App\Models\PrizeDraw;
use App\Models\PrizeDrawDrawn;
use App\Trait\EvolutionTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PrizeDrawService
{

    use EvolutionTrait;

    public function search($request)
    {
        try{
            $take = $request->take ?? 10;
            $instance_id = $request->instance_id ?? null;

            $prizeDraws = PrizeDraw::orderBy('id', 'desc');

            if(isset($instance_id)){
                $prizeDraws->where('instance_id', $instance_id);
            }
              
            if($request->filled('prize_name')){
                $prizeDraws->where('prize_name', 'LIKE', "%$request->prize_name%");
            }

            $prizeDraws = $prizeDraws->paginate($take);

            return $prizeDraws;

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
                'groups' => ['required', 'string'],
                'groups_name' => ['required', 'string'],
                'prize_name' => ['required', 'string'],
            ];                                    

            $requestData = $request->all();

            $validator = Validator::make($requestData, $rules);

            if($validator->fails()){
                throw new Exception($validator->errors(), 400);
            }
            
            $instance = Instance::where('external_id', $requestData['instance_id'])->first();
            
            if(!isset($instance)){
                throw new Exception("Instância não encontrada", 400);
            }

            $requestData['instance_name'] = $instance->name;
            
            $prizeDraw = PrizeDraw::create($requestData);


            $groups = explode(',' ,$request->groups);
            $this->prepareEvoCredentials();
            $participants = [];
            foreach ($groups as $group) {
                $response = $this->fetchAllParticipantsGroup($instance->name, $group);
                if (!isset($response['participants'])) {
                    throw new Exception("Participantes não encontrados para o grupo: {$group}");
                }
    
                $participants = array_merge($participants, $response['participants']);
                $participants = array_unique($participants, SORT_REGULAR);
            }
    
            $prizeDraw['participants'] = $participants;
                
            return [
                'status' => true,
                'data' => $prizeDraw
            ];

        } catch(Exception $error){
            return [
                'status' => false,
                'message' => $error->getMessage(),
                'statusCode' => 400
            ];  
        }
    }

    public function addDrawn($request)
    {
        try{

            $rules = [
                'prize_draw_id' => ['required', 'integer'],
                'name' => ['required', 'string'],
                'number' => ['required', 'string'],
            ];                                    

            $requestData = $request->all();

            $validator = Validator::make($requestData, $rules);

            if($validator->fails()){
                throw new Exception($validator->errors(), 400);
            }
            
            $prizeDrawDrawn = PrizeDrawDrawn::create($requestData);
                
            return [
                'status' => true,
                'data' => $prizeDrawDrawn
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
            $prizeDraw = PrizeDraw::find($id);

            if(!isset($prizeDraw)) {
                throw new Exception('Sorteio não encontrado');
            }

            $duplicatePrizeDraw = $prizeDraw->replicate();
            $duplicatePrizeDraw->save();

            return [
                'status' => true,
                'data' => $duplicatePrizeDraw
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
            $rules = [
                'instance_id' => ['required', 'string'],
                'groups' => ['required', 'string'],
                'groups_name' => ['required', 'string'],
                'prize_name' => ['required', 'string'],
            ];                                    

            $requestData = $request->all();

            $validator = Validator::make($requestData, $rules);

            if($validator->fails()){
                throw new Exception($validator->errors(), 400);
            }

            $prizeDrawToUpdate = PrizeDraw::find($id);

            if(!isset($prizeDrawToUpdate)){
                throw new Exception('Sorteio não encontrado');
            }
            
            $prizeDrawToUpdate->update($requestData);
                
            return [
                'status' => true,
                'data' => $prizeDrawToUpdate
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
            
            $prizeDraw = PrizeDraw::find($id);

            if(!isset($prizeDraw)){
                throw new Exception('Sorteio não encontrado');
            }

            $prizeDrawId = $prizeDraw->id;

            $prizeDraw->delete();

            return [
                'status' => true,
                'data' => $prizeDrawId
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
