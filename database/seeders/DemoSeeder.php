<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy đúng 2 user đã được tạo & gán role trong AdminAuthorSeeder
        $admin  = User::where('email', 'admin@example.com')->first();
        $author = User::where('email', 'author@example.com')->first();

        // Nếu vì lý do nào đó chưa có, tạo tối thiểu (KHÔNG đụng tới cột 'role')
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]);
            // $admin->assignRole('admin'); // chỉ gọi nếu muốn, nhưng AdminAuthorSeeder đã gán rồi
        }

        if (!$author) {
            $author = User::create([
                'name' => 'Author',
                'email' => 'author@example.com',
                'password' => Hash::make('author123'),
                'email_verified_at' => now(),
            ]);
            // $author->assignRole('author');
        }

        // Category mẫu
        $cat = Category::firstOrCreate(
            ['slug' => 'thoi-su'],
            ['name' => 'Thời sự']
        );

        // Post mẫu
        Post::firstOrCreate(
            ['slug' => 'bai-mau'],
            [
                'title'       => 'Bài mẫu',
                'content'     => 'Nội dung bài mẫu',
                'category_id' => $cat->id,
                'user_id'     => $author->id,
                'status'      => 'draft',
            ]
        );
    }
}
