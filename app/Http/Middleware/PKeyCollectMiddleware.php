<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PKeyCollectMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization');
        if (!$header || !str_contains($header, 'Bearer')) {
            return $next($request);
        }

        $token = Str::replace('Bearer ', '', $header);
        $data = json_decode(base64_decode(explode('.', $token)[1] ?? ''), true);
        blink()->put('pkey', $data['pkey'] ?? null);

        return $next($request);
    }
}
