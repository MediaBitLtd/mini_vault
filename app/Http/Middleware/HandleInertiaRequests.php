<?php

namespace App\Http\Middleware;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth.user' => Auth::check() ? UserResource::make(Auth::user())->toArray($request) : null,
            'app.isLocal' => App::isLocal(),
            'app.version' => config('app.version'),
            'csrf_token' => csrf_token(),
        ]);
    }
}
