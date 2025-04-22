<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AssertPKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        throw_if(! blink()->get('pkey'), AuthorizationException::class);

        return $next($request);
    }
}
