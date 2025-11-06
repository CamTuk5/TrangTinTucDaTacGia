<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Bài viết</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Inter,Helvetica,Arial,sans-serif;margin:0;padding:40px;background:#0f172a;color:#e2e8f0}
        .wrap{max-width:980px;margin:0 auto}
        .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px}
        .card{background:#0b1220;border:1px solid #1f2937;border-radius:14px;padding:18px}
        .meta{font-size:12px;color:#94a3b8;margin-top:6px}
        a{color:#93c5fd;text-decoration:none}
        header{margin-bottom:18px;display:flex;justify-content:space-between;gap:12px;align-items:center}
        input,select{background:#0b1220;border:1px solid #334155;border-radius:10px;color:#e2e8f0;padding:10px}
        form{display:flex;gap:10px}
    </style>
</head>
<body>
<div class="wrap">
    <header>
        <h2>Danh sách bài viết</h2>
        <form method="get" action="{{ url('/posts') }}">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm tiêu đề...">
            <select name="sort">
                <option value="published_at" @selected(request('sort')==='published_at')>Mới nhất</option>
                <option value="views" @selected(request('sort')==='views')>Lượt xem</option>
                <option value="title" @selected(request('sort')==='title')>Tiêu đề</option>
            </select>
            <select name="dir">
                <option value="desc" @selected(request('dir')!=='asc')>Giảm dần</option>
                <option value="asc" @selected(request('dir')==='asc')>Tăng dần</option>
            </select>
            <button type="submit">Lọc</button>
        </form>
    </header>

    <div class="grid">
        @foreach($posts as $post)
            <div class="card">
                <h3><a href="{{ url('/posts/'.$post->slug) }}">{{ $post->title }}</a></h3>
                <div class="meta">
                    {{ $post->category->name ?? 'Chưa phân loại' }} •
                    {{ $post->user->name ?? '---' }} •
                    {{ $post->published_at ? $post->published_at->diffForHumans() : 'Nháp' }} •
                    {{ $post->views }} lượt xem
                </div>
            </div>
        @endforeach
    </div>

    <div style="margin-top:16px">
        {{ $posts->withQueryString()->links() }}
    </div>
</div>
</body>
</html>
