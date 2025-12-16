<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents; // Added

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Profile
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'is_approved' => true,
                'role' => 'admin'
            ]
        );

        // Normal User Profile
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Usuário Comum',
                'password' => Hash::make('password'),
                'is_approved' => true,
                'role' => 'user'
            ]
        );
    }
}
