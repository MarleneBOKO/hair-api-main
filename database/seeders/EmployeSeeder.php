<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('employes')->insert([
            [
                'id_employe' => Str::uuid(),
                'name' => 'Jean Dupont',
                'skills' => 'expert',
                'description' => 'Coiffeur spécialisé en coupe afro.',
                'image' => 'jean.jpg',
                'hiring_date' => '2022-05-10',
                'departure_date' => null,
                'work_hours' => '08:00 - 18:00',
                'salary' => 1200.50,
                'status' => 'Actif',
                'phone' => '+229 99829540',
                'email' => 'yeyeyz@gmail.com',
                'user_id' => '9cad3329-7dd3-4851-b5a4-59d0f10c2447', // Remplace par un UUID valide
                'salon_id' => '5824ce92-883b-44b8-ac85-1a2bb4784693', // Remplace par un UUID valide
            ],
            [
                'id_employe' => Str::uuid(),
                'name' => 'Marie Curie',
                'skills' => 'moyen',
                'description' => 'Coloriste professionnelle.',
                'image' => 'marie.jpg',
                'hiring_date' => '2023-02-15',
                'departure_date' => null,
                'work_hours' => '10:00 - 20:00',
                'salary' => 1000.00,
                'status' => 'Actif',
                 'phone' => '+229 99829540',
                'email' => 'yeyeyz@gmail.com',
                'user_id' => '9cad3329-7dd3-4851-b5a4-59d0f10c2447', // Remplace par un UUID valide
                'salon_id' => '5824ce92-883b-44b8-ac85-1a2bb4784693', // Remplace par un UUID valide
            ],
        ]);
    }
}
