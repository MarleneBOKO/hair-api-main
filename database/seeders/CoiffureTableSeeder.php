<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoiffureTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
       // Chemin vers le dossier contenant les images
    $imageDirectory = 'coiffure_store/femme/';
    $imageDirectory2 = 'others_images/femme/';

    // Boucle sur les images dans le dossier
    for ($i = 1; $i <= 31; $i++) {
        // Calculer les indices pour les images supplémentaires
        $j = ($i - 1) % 26 + 1;
        $x = $j % 26 + 1;

        // Insérer les données dans la base de données
        DB::table('coiffures')->insert([
            'id_coiffure' => \Illuminate\Support\Str::uuid(),
            'name' => 'Coiffure ' . $i,
            'description' => 'Description de la coiffure ' . $i,
            'category' => 'Femme',
            'image' => $imageDirectory . "coiffure_{$i}.jpg",
            'image1' => $imageDirectory2 . "femme_{$j}.jpeg",
            'image2' => $imageDirectory2 . "femme_{$x}.jpeg",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

        // Chemin vers le dossier contenant les images
       $imageDirectory = 'coiffure_store/enfant/'; 

        // Boucle sur les images dans le dossier
        for ($i = 1; $i <= 18; $i++) {
            $j = ($i - 1) % 26 + 1;
            $x = $j % 26 + 1;
          
            // Insérer les données dans la base de données
            DB::table('coiffures')->insert([
                'id_coiffure' => \Illuminate\Support\Str::uuid(),
                'name' => 'Enfant ' . $i,
                'description' => 'Description de la coiffure ' . $i,
                'category' => 'Enfant',
                'image' => $imageDirectory . "enfant_{$i}.jpg",
                 'image1' => $imageDirectory2 . "femme_{$j}.jpeg",
                 'image2' => $imageDirectory2 . "femme_{$x}.jpeg",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    }

   // Chemin vers le dossier contenant les images
    $imageDirectory = 'coiffure_store/homme/';

    // Boucle sur les images dans le dossier
    for ($i = 1; $i <= 25; $i++) {
        $j = ($i - 1) % 26 + 1;
        $x = $j % 26 + 1;
        // Insérer les données dans la base de données
        DB::table('coiffures')->insert([
            'id_coiffure' => \Illuminate\Support\Str::uuid(),
            'name' => 'Homme ' . $i,
            'description' => 'Description de la coiffure ' . $i,
            'category' => 'Homme',
            'image' => $imageDirectory . "homme_{$i}.jpg",
            'image1' => $imageDirectory2 . "femme_{$j}.jpeg",
            'image2' => $imageDirectory2 . "femme_{$x}.jpeg",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }



}
}
