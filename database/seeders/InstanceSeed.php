<?php

namespace Database\Seeders;

use App\Models\Instance;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class InstanceSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $instances = [
            [
                'name' => 'pedroh',
                'external_id' => '011eeeb0-0578-4d78-9dbd-4ba57b5fc000',
                'api_key' => "044024DA0D50-4DAF-8392-3EBC6226EEFF",                
            ],
            [
                'name' => 'guimadureira',
                'external_id' => '5edf2a1b-03a7-473d-9f5a-50590501bcd5"',
                'api_key' => "C8DA2C05343E-4107-98A5-B2F57B0ADFD9",  
            ]
        ];

        $user = User::where('email', 'admin@admin')->first();

        foreach($instances as $instance){
            $instance['user_id'] = $user->id;
            Instance::firstOrcreate($instance, $instance);
        }
    }
}
