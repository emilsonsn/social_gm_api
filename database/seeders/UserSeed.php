<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
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
        
        User::firstOrCreate([
            'email' => 'admin@admin',
        ],
        [
            'name' => 'Admin',
            'password' => Hash::make('admin'),
            'phone' => '83991236636',
            'cpf_cnpj' => '13754674412',
            'birth_date' => '2001-12-18',
            'is_active' => true,
            'role' => UserRoleEnum::Admin->value,
        ]);

        User::firstOrCreate([
            'email' => 'user@user',
        ],
        [
            'name' => 'User',
            'password' => Hash::make('user'),
            'phone' => '83991236636',
            'cpf_cnpj' => '13754674412',
            'birth_date' => '2001-12-18',
            'is_active' => true,
            'role' => UserRoleEnum::User->value,
        ]);
    }
}
