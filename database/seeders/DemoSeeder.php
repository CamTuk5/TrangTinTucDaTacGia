<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $cats = collect(['Công nghệ','Giải trí','Thể thao','Giáo dục','Du lịch'])->map(function ($n) {
            return Category::updateOrCreate(['slug' => Str::slug($n)], ['name' => $n]);
        });

        $authors = User::where('role', 'author')->get();
        if ($authors->isEmpty()) {
            $authors = User::factory()->author()->count(2)->create();
        }

        foreach (range(1, 10) as $i) {
            $author = $authors->random();
            $title = 'Bài viết mẫu '.$i;
            $post = Post::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'user_id' => $author->id,
                    'category_id' => $cats->random()->id,
                    'title' => $title,
                    'content' => 'Nội dung mẫu cho bài viết '.$i.'.',
                    'status' => $i % 2 === 0 ? 'published' : 'draft',
                    'published_at' => $i % 2 === 0 ? now()->subDays(11 - $i) : null,
                    'views' => random_int(0, 50),
                ]
            );

            foreach (range(1, 3) as $c) {
                Comment::updateOrCreate(
                    ['post_id' => $post->id, 'user_id' => $author->id, 'content' => "Bình luận $c cho bài $i"],
                    ['status' => 'approved']
                );
            }
        }

        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $published = Post::where('status', 'published')->get();
            foreach ($published as $p) {
                Vote::updateOrCreate(
                    ['post_id' => $p->id, 'user_id' => $admin->id],
                    ['choice' => random_int(1, 2)]
                );
            }
        }
    }
}
