<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectAdminsMiddleware
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        if ($request->user()->is_admin && !str_starts_with($request->path(), 'admin')) {
            return redirect('/admin');
        }

        return $next($request);
    }
}
