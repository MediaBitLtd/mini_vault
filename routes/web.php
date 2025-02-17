<?php

use App\Actions\Auth\HandleAuthenticationCallback;
use App\Actions\Auth\HandleLoginSubmission;
use App\Actions\Auth\ShowLoginPage;
use App\Actions\Dashboard\ShowDashboardPage;
use App\Actions\Groups\ShowAll;
use App\Actions\Groups\ShowFavourites;
use App\Actions\Settings\ShowSettingsPage;
use App\Actions\Vaults\DeleteVault;
use App\Actions\Vaults\ShowVault;
use Illuminate\Support\Facades\Route;

Route::get('auth/login', ShowLoginPage::class)->name('login');
Route::post('auth/login', HandleLoginSubmission::class)->name('login.submit');
Route::get('auth/logout', function () {
    session()->flush();
    return redirect()->route('login');
});

Route::get('auth/callback', HandleAuthenticationCallback::class)->middleware('auth')->name('auth.callback');

Route::get('/', ShowDashboardPage::class)->name('dashboard')->middleware('auth');


Route::middleware('auth')->group(function () {
    Route::get('all', ShowAll::class);
    Route::get('favourites', ShowFavourites::class);

    // Vaults
    Route::get('vault/{vault}', ShowVault::class);
    Route::delete('vault/{vault}', DeleteVault::class);

    Route::get('settings', ShowSettingsPage::class);
});
