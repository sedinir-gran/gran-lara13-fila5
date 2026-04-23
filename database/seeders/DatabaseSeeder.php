<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrador Teste',
            'email' => 'admin@mail.com',
            'password' => bcrypt('12345678'),
            'is_admin' => true,
        ]);

        User::create([
            'name' => 'Usuário Teste',
            'email' => 'usuario@mail.com',
            'password' => bcrypt('12345678'),
            'is_admin' => false,
        ]);
    }
}
