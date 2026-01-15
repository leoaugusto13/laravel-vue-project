<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegistrationFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $training = \App\Models\Training::first();
        if ($training) {
             $training->registrationForm()->create([
                'title' => 'Ficha de Inscrição Exemplo',
                'description' => 'Esta é uma ficha criada automaticamente para testes.',
                'published' => true
            ]);
            $this->command->info('Registration Form created for Training ID: ' . $training->id);
        } else {
            $this->command->warn('No training found to attach the form to.');
        }
    }
}
