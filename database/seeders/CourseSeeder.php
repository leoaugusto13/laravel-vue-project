<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Course::create([
            'title' => 'Introdução ao Laravel',
            'description' => 'Aprenda os fundamentos do framework PHP mais popular.',
            'status' => 'active',
            'start_date' => now()->addDays(5)
        ]);

        Course::create([
            'title' => 'Vue.js Completo',
            'description' => 'Domine o Vue 3 criando aplicações reais.',
            'status' => 'active',
            'start_date' => now()->addDays(10)
        ]);

        Course::create([
            'title' => 'Gestão Administrativa',
            'description' => 'Curso especial para administradores do sistema.',
            'status' => 'inactive',
            'start_date' => now()->addDays(20)
        ]);
    }
}
