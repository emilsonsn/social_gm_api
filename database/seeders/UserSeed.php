<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\Instance;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void

    {
        $users = [
            [
                'email' => 'admin@admin',
                'name' => 'Admin',
                'password' => Hash::make('admin'),
                'phone' => '83991236636',
                'cpf_cnpj' => '13754674412',
                'birth_date' => '2001-12-18',
                'is_active' => true,
                'role' => UserRoleEnum::Admin->value,
            ],
            [
                'email' =>  'gm@guimadureira.com',
                'name' => 'Guilherme',
                'password' => Hash::make('@sfmG2024'),
                'phone' => '',
                'cpf_cnpj' => '13754674411',
                'birth_date' => '2001-12-18',
                'is_active' => true,
                'role' => UserRoleEnum::User->value,
            ],
            [
                'email' => 'ph@guimadureira.com',
                'name' => 'Pedro',
                'password' => Hash::make('Polinho2019'),
                'phone' => '',
                'cpf_cnpj' => '13754674414',
                'birth_date' => '2001-12-18',
                'is_active' => true,
                'role' => UserRoleEnum::User->value,
            ]
        ];

        $instances = [
            [
                'instance' => 'guimadureira',
                'email'    => 'gm@guimadureira.com'
            ],
            [
                'instance' => 'pedroh',
                'email' => 'ph@guimadureira.com',
            ]
        ];

        foreach ($users as $user) {
            $userCreated = User::updateOrCreate([
                'email' => $user['email'],
            ], $user);

            $userInstance = collect($instances)->firstWhere('email', $user['email']);

            if ($userInstance) {
                Instance::where('name', $userInstance['instance'])
                    ->update([
                        'user_id' => $userCreated->id,
                    ]);
            }
        } 
    }
}
