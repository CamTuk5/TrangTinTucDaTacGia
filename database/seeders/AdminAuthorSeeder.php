<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminAuthorSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo role nếu chưa có
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $authorRole = Role::firstOrCreate(['name' => 'author']);

        // Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($adminRole);

        // Author
        $author = User::updateOrCreate(
            ['email' => 'author@example.com'],
            [
                'name' => 'Author',
                'password' => Hash::make('author123'),
                'email_verified_at' => now(),
            ]
        );
        $author->assignRole($authorRole);

        $this->command->info('Seeded: admin@example.com (admin123), author@example.com (author123)');
    }
}
