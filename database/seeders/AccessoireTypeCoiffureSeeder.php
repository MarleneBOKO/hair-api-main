<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccessoireTypeCoiffureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accessoires = DB::table('accessoires')->pluck('id_accessory');
        $hairstyleTypes = DB::table('type_coiffures')->pluck('id_hairstyle_type');
        $salons = DB::table('salons')->pluck('id_salon');

        foreach ($salons as $salonId) {
            foreach ($hairstyleTypes as $hairstyleTypeId) {
                foreach ($accessoires as $accessoryId) {
                    DB::table('accessoire_type_coiffures')->insert([
                        [
                            'uuid' => Str::uuid(),
                            'accessory_id' => $accessoryId,
                            'hairstyle_type_id' => $hairstyleTypeId,
                            'salon_id' => $salonId,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'nb_accessory' => 2
                        ],
                    ]);
                }
            }
        }
    }
}
