<?php

use Illuminate\Support\Facades\Route;
use App\Models\Post;
use App\Models\Category;
use App\Models\Comment;

Route::get('/', fn () => view('welcome'))->name('home');

Route::get('/posts', function () {
    $q = request('q');
    $sort = request('sort', 'published_at');
    $dir = request('dir', 'desc');
    $posts = Post::with(['user', 'category'])
        ->published()
        ->when($q, fn ($qq) => $qq->where('title', 'like', "%$q%"))
        ->orderBy($sort, $dir)
        ->paginate(9);
    return view('posts', compact('posts'));
})->name('web.posts');

Route::get('/posts/{slug}', function (string $slug) {
    $post = Post::with(['user', 'category', 'comments.user'])
        ->where('slug', $slug)
        ->firstOrFail();
    $comments = $post->comments()->where('status', 'approved')->latest()->paginate(5);
    return view('comments', compact('post', 'comments'));
})->name('web.posts.show');

Route::get('/admin', function () {
    $pendingPosts = Post::with('user')->where('status', 'draft')->latest()->take(20)->get();
    $categories = Category::orderBy('name')->get();
    $stats = [
        'posts' => Post::count(),
        'published' => Post::where('status', 'published')->count(),
        'drafts' => Post::where('status', 'draft')->count(),
        'comments' => Comment::count(),
    ];
    return view('admin', compact('pendingPosts', 'categories', 'stats'));
})->name('web.admin');
