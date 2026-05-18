<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Mundial',
            'email' => 'admin@mundial.com',
            'password' => Hash::make('admin123'),
            'is_admin' => true, 
        ]);

        User::factory(10)->create();
    }
}