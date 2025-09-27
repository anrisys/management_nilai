<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SiswaSeeder::class);

        User::factory()->create([
            'name' => 'Demo Admin',
            'email' => 'demo@example.com',
            'password' => Hash::make('demo12345')
        ]);
    }
}
