<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TypeCoiffureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salons = DB::table('salons')->pluck('id_salon');
        $coiffures = DB::table('coiffures')->get();

        foreach ($salons as $salonId) {
            foreach ($coiffures as $coiffure) {
                DB::table('type_coiffures')->insert([
                    [
                        'id_hairstyle_type' => Str::uuid(),
                        'name' => $coiffure->name,
                        'description' => $coiffure->description,
                        'category' => $coiffure->category,
                        'image' => $coiffure->image,
                        'image1' => $coiffure->image1,
                        'image2' => $coiffure->image2,
                        'price' => 100.0,
                        'salon_id' => $salonId,
                        'coiffure_id' => $coiffure->id_coiffure,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'nb_employe' => 2
                    ],
                ]);
            }
        }
    }
}
