<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Trang tin tức đa tác giả</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Inter,Helvetica,Arial,sans-serif;margin:0;padding:40px;background:#0f172a;color:#e2e8f0}
        a{color:#93c5fd;text-decoration:none}
        .wrap{max-width:960px;margin:0 auto}
        .card{background:#0b1220;border:1px solid #1f2937;border-radius:14px;padding:24px;margin-bottom:16px}
        .actions a{display:inline-block;margin-right:12px;padding:10px 14px;border:1px solid #334155;border-radius:10px}
        h1{margin:0 0 16px 0}
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>Trang tin tức đa tác giả</h1>
        <div class="actions">
            <a href="{{ url('/posts') }}">Danh sách bài viết</a>
            <a href="{{ url('/admin') }}">Quản trị</a>
            <a href="{{ url('/api/posts') }}">API Posts</a>
            <a href="{{ url('/api/categories') }}">API Categories</a>
        </div>
    </div>
</div>
</body>
</html>
