<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@ticto.com',
            'password' => Hash::make('123456'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'employer',
            'email' => 'employer@ticto.com',
            'password' => Hash::make('123456'),
            'role' => 'employer',
            'cpf' => '123.456.789-09',
            'cargo' => 'Desenvolvedor',
            'data_nascimento' => '1990-05-15',
            'admin_id' => 1,
        ]);
    }
}
