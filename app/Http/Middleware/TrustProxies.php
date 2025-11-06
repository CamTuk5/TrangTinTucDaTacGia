<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Symfony\Component\HttpFoundation\Request;

class TrustProxies extends Middleware
{
    /**
     * Tin cậy tất cả proxy (phù hợp khi bạn tự kiểm soát hạ tầng:
     * Nginx/Render/Cloudflare/ngrok...). Nếu muốn chặt hơn,
     * hãy liệt kê IP proxy cụ thể dạng mảng.
     *
     * Ví dụ chặt hơn:
     * protected $proxies = ['10.0.0.2', '10.0.0.3'];
     */
    protected $proxies = '*';

    /**
     * Các header chuẩn để Laravel hiểu đúng client IP / scheme HTTPS.
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR
        | Request::HEADER_X_FORWARDED_HOST
        | Request::HEADER_X_FORWARDED_PORT
        | Request::HEADER_X_FORWARDED_PROTO
        | Request::HEADER_X_FORWARDED_AWS_ELB;
}
