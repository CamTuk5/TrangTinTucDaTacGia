<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    public function hosts()
    {
        return [
            // 1) Tất cả subdomain của APP_URL (ví dụ https://mysite.com)
            $this->allSubdomainsOfApplicationUrl(),

            // 2) Local dev
            'localhost',
            '127.0.0.1',
            '::1',

            // 3) Nếu dùng ngrok (regex cho mọi subdomain *.ngrok-free.app)
            '^[a-z0-9-]+\.ngrok\-free\.app$',

            // 4) Nếu deploy Vercel/Render (chỉnh theo domain của bạn)
            // '^[a-z0-9-]+\.vercel\.app$',
            // '^your-custom-domain\.com$',
            // '^[a-z0-9-]+\.onrender\.com$',
        ];
    }
}
