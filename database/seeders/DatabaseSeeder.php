<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
                'name' => 'Andi',
                'username' => 'andi123',
                'password' => Hash::make('password123'),
                'role' => 'customer',
            ]);

        User::create([
            'name' => 'Budi',
            'username' => 'budi123',
            'password' => Hash::make('password123'),
            'role' => 'kantin',
        ]);
    }
}
