<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'id_user' => \Illuminate\Support\Str::uuid(),
            'name' => 'Joe',
            'email' => 'mahukolo547@gmail.com',
            'password' => Hash::make('azertyuiop'),
            'user_type' => 'Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
