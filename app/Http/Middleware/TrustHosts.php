<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    public function hosts()
    {
        return [
            $this->allSubdomainsOfApplicationUrl(),
            'localhost',
            '127.0.0.1',
            '::1',
        ];
    }
}
