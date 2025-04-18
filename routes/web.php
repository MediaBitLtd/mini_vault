<?php

use App\Actions\Auth\ChangePassword;
use App\Actions\Auth\HandleAuthenticationCallback;
use App\Actions\Auth\HandleLoginSubmission;
use App\Actions\Auth\ShowLoginPage;
use App\Actions\Dashboard\OnboardSubmit;
use App\Actions\Dashboard\ShowDashboardPage;
use App\Actions\Dashboard\ShowOnboardPage;
use App\Actions\Groups\ShowFavourites;
use App\Actions\Settings\ShowSettingsPage;
use App\Actions\Vaults\DeleteVault;
use App\Actions\Vaults\ShowVault;
use App\Http\Middleware\AssertPKeyMiddleware;
use App\Http\Middleware\RedirectAdminsMiddleware;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('auth/login', ShowLoginPage::class)->name('login');
Route::post('auth/login', HandleLoginSubmission::class)->name('login.submit');
Route::get('auth/logout', function () {
    session()->flush();
    return redirect()->route('login');
});

Route::post('auth/verify', function () {
    Auth::guard('web')->login(Auth::guard('api')->user());
    return redirect()->route('dashboard');
})->middleware('auth:api');

Route::post('auth/change-password', ChangePassword::class)
    ->middleware(['auth:api', AssertPKeyMiddleware::class])
    ->name('change-password');

Route::get('auth/callback', HandleAuthenticationCallback::class)->middleware('auth')->name('auth.callback');

Route::middleware(['auth', RedirectAdminsMiddleware::class])->group(function () {
    Route::get('/', ShowDashboardPage::class)->name('dashboard');

    Route::get('onboard', ShowOnboardPage::class)->name('onboard');

    Route::get('favourites', ShowFavourites::class);

    // Vaults
    Route::get('vault/{vault}', ShowVault::class);
    Route::delete('vault/{vault}', DeleteVault::class);

    Route::get('settings', ShowSettingsPage::class);
});

// TODO refactor this ofc
Route::get('.well-known/webauthn', function () {
    return response()->json([
        'origins' => [
            config('app.url'),
        ],
    ]);
});

Route::get('/manifest.json', function () {
    $name = match (App::environment()) {
        'local' => 'MiniVault (local)',
        'staging' => 'MiniVault (staging)',
        'production' => 'MiniVault',
    };

    return [
        'short_name' => $name,
        'name' => $name,
        'id' => '/?source=pwa',
        'start_url' => '/?source=pwa',
        'display' => 'standalone',
        'icons' => [
            [
                'src' => '/img/logo.svg',
                'type' => 'image/svg+xml',
                'sizes' => '512x512',
            ],
        ],
        'scope' => '/',
        'background_color' => '#0c0a09',
        'theme_color' => '#0c0a09',
        'description' => 'MiniVault Project',
        'categories' => [
            'productivity',
            'utilities',
        ],
    ];
});
