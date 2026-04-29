<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class CspMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        Vite::useCspNonce();

        $nonce = Vite::cspNonce();
        $self = "'self'";
        $noncePart = $nonce ? "'nonce-{$nonce}'" : '';

        $policy = implode('; ', array_filter([
            "default-src {$self}",
            "script-src {$self} {$noncePart}",
            "style-src {$self} {$noncePart} 'unsafe-inline'",
            "img-src {$self} data: blob:",
            "font-src {$self} data:",
            "connect-src {$self}",
            "frame-ancestors 'none'",
        ]));

        return $next($request)->withHeaders([
            'Content-Security-Policy' => $policy,
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
        ]);
    }
}
