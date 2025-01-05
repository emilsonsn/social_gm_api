<?php

namespace App\Services\Routine;

use App\Models\Automation;
use App\Models\Instance;
use App\Trait\EvolutionTrait;

class WebhookService
{

    use EvolutionTrait; 

    public function handleMessage($request){
        if($request['event'] === 'group-participants.update'){
            switch($request['data']['action']){
                case 'add':
                    $this->addParticipant($request);
                    break;
                case 'remove':
                    $this->removeParticipant($request);
                    break;
            }
        }       
    }

    private function addParticipant($data){
        $instance = Instance::where('name', $data['instance'])->first();

        if(!isset($instance)) return;

        $automation = Automation::where('instance_id', $instance->external_id)
            ->whereNotNull('welcome_message')
            ->first();            

        if(!isset($automation)) return;

        if($automation->group_id != $data['data']['id']) return;        

        $instanceName = $instance->name;
        $message = $automation->welcome_message;
        $number = $data['data']['participants'][0];
        $this->sendMessage($instanceName, $number, $message, false);
    }

    private function removeParticipant($data){
        $instance = Instance::where('name', $data['instance'])->first();

        if(!isset($instance)) return;

        $automation = Automation::where('instance_id', $instance->external_id)
            ->whereNotNull('farewell_message')
            ->first();

        if(!isset($automation)) return;

        if($automation->group_id != $data['data']['id']) return;

        $instanceName = $instance->name;
        $message = $automation->farewell_message;
        $number = $data['data']['participants'][0];
        $this->sendMessage($instanceName, $number, $message, false);
    }

    // public function handle($request)
    // {
    //     try{
    //         if(!$request->input('token') == env('PERFECT_PAY_TOKEN')){
    //             return ['status' => 403, 'message' => "Token inválido"];
    //         }        
            
    //         $data = $request->all();
    
    //         switch ($data['subscription']['status']) {
    //             case 'active':
    //                 $user = User::where('email', $data['customer']['email'])->first();
    //                 if($user){
    //                     $this->unblockUser($user);
    //                 }else{
    //                     $this->createUser($data['customer']);
    //                 }
    //                 break;
    //             case 'cancelled':
    //                 $this->blockUser($data['customer']['email']);
    //                 break;
    //             case 'canceled':
    //                 $this->blockUser($data['customer']['email']);
    //                 break;                
    //             case 'expired':
    //                 $this->blockUser($data['customer']['email']);
    //                 break;                
    //             default:
    //                 Log::warning('Evento desconhecido recebido do webhook: ' . $data['subscription']['status']);
    //         }
    
    //         return response()->json(['status' => 'success'], 200);
    //     }catch(Exception $error){
    //         Log::error('handle: ' . $error->getMessage());
    //     }
    // }

    // private function createUser($customer)
    // {
    //     try{
    //         $full_name = [];
    //         foreach(explode(' ',$customer['full_name']) as $nome){
    //             if(in_array($nome, ['de', 'das', 'dos', 'da', 'do'])) continue;
    //             $full_name[] = $nome;
    //         }

    //         $name = trim($full_name[0] . ' ' . (isset($full_name[1]) ? $full_name[1] : ''));

    //         $password = Str::random(10);
            
    //         $user = User::create([
    //             'name' => $name,
    //             'email' => $customer['email'],
    //             'password' => bcrypt($password),
    //             'token' => (string) Str::uuid(),
    //             'database_name' => 'tenant_' . Str::random(10),
    //             'is_active' => true
    //         ]);
    
    //         // Enviar email de boas-vindas
    //         Mail::to($user->email)->send(new WelcomeMail($user, $password));
    
    //         // Criar banco de dados do tenant
    //         DB::statement("CREATE DATABASE `{$user->database_name}`");
    //         $this->runSqlScript($user->database_name);
    
    //         Log::info('Usuário criado e banco de dados configurado: ' . $user->email);
    
    //         return ['status' => true, 'usuario criado com sucesso'];
    //     }catch(Exception $error){
    //         Log::error('createUser: ' . $error->getMessage());
    //     }
    // }

    // private function blockUser($email)
    // {
    //     try{
    //         $user = User::where('email', $email)->first();
    //         if ($user) {
    //             $user->update(['is_active' => false]);
    //             Log::info('Usuário bloqueado: ' . $user->email);
    //             return ['status' => true, "$user->name desativado"];
    //         }
    
    //         return ['status' =>false, 'usuario não encontrado'];
    //     }
    //     catch(Exception $error){
    //         Log::error('blockUser: ' . $error->getMessage());
    //     }
    // }

    // private function unblockUser($user)
    // {
    //     try{
    //         if ($user) {
    //             $user->update(['is_active' => true]);
    //             Log::info('Usuário ativado: ' . $user->email);
    //             return ['status' => true, "$user->name ativado"];
    //         }
    
    //         return ['status' =>false, 'usuario não encontrado'];
    //     }
    //     catch(Exception $error){
    //         Log::error('unblockUser: ' . $error->getMessage());
    //     }
    // }

    // private function runSqlScript($databaseName)
    // {
    //     try{
    //         $scriptPath = Storage::path('sql/create_tenant_db.sql');
    //         if (!file_exists($scriptPath)) {
    //             throw new \Exception("Arquivo SQL não encontrado: {$scriptPath}");
    //         }
    //         $script = file_get_contents($scriptPath);
    //         config(['database.connections.tenant.database' => $databaseName]);
    //         DB::purge('tenant');
    //         DB::reconnect('tenant');
    //         DB::connection('tenant')->unprepared($script);
    //         DB::disconnect('tenant');
    //     }
    //     catch(Exception $error){
    //         Log::error('runSqlScript: ' . $error->getMessage());
    //     }
    // }
}
