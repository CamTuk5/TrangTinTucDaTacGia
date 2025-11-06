<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Bình luận</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Inter,Helvetica,Arial,sans-serif;margin:0;padding:40px;background:#0f172a;color:#e2e8f0}
        .wrap{max-width:820px;margin:0 auto}
        .card{background:#0b1220;border:1px solid #1f2937;border-radius:14px;padding:18px;margin-bottom:14px}
        textarea{width:100%;min-height:100px;background:#0b1220;border:1px solid #334155;border-radius:10px;color:#e2e8f0;padding:10px}
        button{padding:10px 14px;border:1px solid #334155;border-radius:10px;background:#0b1220;color:#e2e8f0}
        .meta{font-size:12px;color:#94a3b8}
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h2>{{ $post->title }}</h2>
        <div class="meta">{{ $post->category->name ?? '' }} • {{ $post->published_at ? $post->published_at->toDateTimeString() : 'Nháp' }}</div>
    </div>

    <div class="card">
        <h3>Thêm bình luận</h3>
        <form method="post" action="{{ url('/api/posts/'.$post->id.'/comments') }}">
            <textarea name="content" placeholder="Nội dung bình luận..."></textarea>
            <div style="margin-top:10px">
                <button type="submit">Gửi</button>
            </div>
        </form>
    </div>

    @foreach($comments as $c)
        <div class="card">
            <div class="meta">{{ $c->user->name ?? 'User' }} • {{ $c->created_at->diffForHumans() }}</div>
            <div style="margin-top:8px">{{ $c->content }}</div>
        </div>
    @endforeach

    <div style="margin-top:12px">
        {{ $comments->links() }}
    </div>
</div>
</body>
</html>
