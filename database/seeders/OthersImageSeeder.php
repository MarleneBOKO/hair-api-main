<?php

namespace Database\Seeders;

use App\Models\Coiffure;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OthersImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Chemin relatif des images
        $imageDirectory = 'storage/coiffure_store/others_images/femme/';
        $totalImages = 26;
        $imagesPerCoiffure = 2;

        // Récupérer tous les coiffures
        $coiffures = Coiffure::all();

        $imageIndex = 1;
        foreach ($coiffures as $coiffure) {
            // Assigner deux images à chaque coiffure
            for ($i = 1; $i <= $imagesPerCoiffure; $i++) {
                // Chemin relatif de l'image
                $imagePath = $imageDirectory . "femme_{$imageIndex}.jpg";

                // Insérer les données dans la base de données
                DB::table('others_images')->insert([
                    'id_others' => \Illuminate\Support\Str::uuid(),
                    'image' => $imagePath,
                    'coiffure_id' => $coiffure->id_coiffure,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $imageIndex++;
                if ($imageIndex > $totalImages) {
                    $imageIndex = 1;
                }
            }
        }
    }
}
