<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Quản trị</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Inter,Helvetica,Arial,sans-serif;margin:0;padding:40px;background:#0f172a;color:#e2e8f0}
        .wrap{max-width:1024px;margin:0 auto}
        .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px}
        .card{background:#0b1220;border:1px solid #1f2937;border-radius:14px;padding:18px}
        table{width:100%;border-collapse:collapse}
        th,td{border-bottom:1px solid #1f2937;padding:10px;text-align:left}
        button{padding:8px 12px;border:1px solid #334155;border-radius:10px;background:#0b1220;color:#e2e8f0}
        a{color:#93c5fd;text-decoration:none}
    </style>
</head>
<body>
<div class="wrap">
    <h2>Bảng điều khiển</h2>

    <div class="grid">
        <div class="card">
            <h3>Bài chờ duyệt</h3>
            <table>
                <thead><tr><th>Tiêu đề</th><th>Tác giả</th><th></th></tr></thead>
                <tbody>
                @forelse($pendingPosts as $p)
                    <tr>
                        <td><a href="{{ url('/posts/'.$p->slug) }}">{{ $p->title }}</a></td>
                        <td>{{ $p->user->name ?? '---' }}</td>
                        <td>
                            <form method="post" action="{{ url('/api/posts/'.$p->id.'/publish') }}">
                                <button type="submit">Publish</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3">Không có bài chờ duyệt</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3>Chuyên mục</h3>
            <ul>
                @foreach($categories as $c)
                    <li>{{ $c->name }} ({{ $c->slug }})</li>
                @endforeach
            </ul>
        </div>

        <div class="card">
            <h3>Thống kê</h3>
            <div>Tổng bài: {{ $stats['posts'] ?? 0 }}</div>
            <div>Đã publish: {{ $stats['published'] ?? 0 }}</div>
            <div>Nháp: {{ $stats['drafts'] ?? 0 }}</div>
            <div>Bình luận: {{ $stats['comments'] ?? 0 }}</div>
        </div>
    </div>
</div>
</body>
</html>
