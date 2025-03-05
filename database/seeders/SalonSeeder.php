<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('salons')->insert([
            'id_salon' => (string) Str::uuid(),
            'salon_name' => 'Salon AZE',
            'address' => 'AKPAKPA',
            'email' => 'olamidenaomie@gmail.com',
            'website' => 'https://salondebeaute.example.com',
            'description' => 'Un salon de beauté haut de gamme offrant une variété de services de coiffure et de soins.',
            'opening_hours' => '09:00 - 19:00',
            'creation_date' => '2024-06-10',
            'last_update_date' => '2024-06-10',
            'longitude' => '2.3522',
            'latitude' => '48.8566',
            'phone_number' => '+22990829540',
            'percent' => 30,
            'percent_cancel' => 10,
            'heure_debut' => '09:00',
            'heure_fin' => '19:00',
            'user_id' => 'fdd7a44b-1899-47bf-9507-04e7c49befd1',
            'subdomain' => 'salonaze1'
        ]);
    }
}
