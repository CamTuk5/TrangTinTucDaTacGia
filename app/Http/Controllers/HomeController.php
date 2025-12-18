<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy bài đã published, sắp xếp theo Heat Score (Logic độc nhất 1)
        $posts = Post::with('author')
            ->where('status', 'published')
            ->orderByDesc('heat_score') 
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('home.index', compact('posts'));
    }

   public function show($slug)
    {
        // Lấy bài viết
        $post = Post::where('slug', $slug)->withCount('comments')->firstOrFail();

        // Tăng view
        $post->increment('views');

        // TÍNH HEAT SCORE
        
        // Dùng abs() để đảm bảo tuổi thọ bài viết luôn là số DƯƠNG (tránh lỗi timezone làm ra số âm)
        $ageInHours = $post->published_at 
            ? abs(now()->diffInHours($post->published_at)) 
            : 0;
            
        // Tính mẫu số (Gravity)
        $gravity = 1.8;
        // Đảm bảo cơ số luôn > 0 để hàm pow không lỗi
        $timeBase = $ageInHours + 2; 
        
        $denominator = pow($timeBase, $gravity);

        // Tính điểm (Numerator / Denominator)
        $numerator = $post->views + ($post->comments_count * 2);
        
        // Kiểm tra tránh chia cho 0
        if ($denominator != 0) {
            $score = $numerator / $denominator;
        } else {
            $score = 0;
        }
        if (is_nan($score) || is_infinite($score)) {
            $score = 0;
        }

        // Lưu vào Database
        $post->update(['heat_score' => $score]);

        return view('home.show', compact('post'));
    }
    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = $category->posts()
            ->with('author')
            ->where('status', 'published')
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('home.index', compact('posts', 'category'));
    }
}
