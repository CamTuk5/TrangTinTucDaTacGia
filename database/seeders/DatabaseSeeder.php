<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminAuthorSeeder::class,
            DemoSeeder::class, // thêm dòng này nếu bạn muốn chạy demo dữ liệu mẫu luôn
        ]);
    }
}
