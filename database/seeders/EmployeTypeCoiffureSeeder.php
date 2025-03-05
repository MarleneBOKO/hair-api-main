<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeTypeCoiffureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employes = DB::table('employes')->pluck('id_employe');
        $hairstyleTypes = DB::table('type_coiffures')->pluck('id_hairstyle_type');

        foreach ($employes as $employeId) {
            foreach ($hairstyleTypes as $hairstyleTypeId) {
                DB::table('employe_type_coiffures')->insert([
                    [
                        'uuid' => Str::uuid(),
                        'duration' => '01:30:00',
                        'employe_id' => $employeId,
                        'hairstyle_type_id' => $hairstyleTypeId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
            }
        }
    }
}
