<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    //     $this->call([
    //         SalonSeeder::class
    //     ]);

    //      $this->call([
    //         CoiffureTableSeeder::class
    //     ]);
    //     $this->call([
    //         TypeCoiffureSeeder::class
    //     ]);
    //     $this->call([
    //         EmployeTypeCoiffureSeeder::class
    //     ]);
    //     $this->call([
    //         AccessoireTypeCoiffureSeeder::class
    //     ]);
    //   /* $this->call([
    //         OthersImageSeeder::class
    //     ]);*/
    //     $this->call([
    //         AdminSeeder::class
    //     ]);

        $this->call([
            EmployeSeeder::class
        ]);
    }
}
