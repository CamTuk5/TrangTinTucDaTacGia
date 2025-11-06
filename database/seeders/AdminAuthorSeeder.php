<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminAuthorSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('admin12345!!'), 'role' => 'admin', 'email_verified_at' => now()]
        );

        User::factory()->author()->count(3)->create();
    }
}
