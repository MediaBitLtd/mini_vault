<?php

use App\Actions\Auth\HandleAuthenticationCallback;
use App\Actions\Auth\HandleLoginSubmission;
use App\Actions\Auth\ShowLoginPage;
use App\Actions\Dashboard\ShowDashboard;
use Illuminate\Support\Facades\Route;

Route::get('/auth/login', ShowLoginPage::class)->name('login');
Route::post('/auth/login', HandleLoginSubmission::class)->name('login.submit');
Route::get('/auth/logout', function () {
    session()->flush();
    return redirect()->route('login');
});

Route::get('/auth/callback', HandleAuthenticationCallback::class)->middleware('auth')->name('auth.callback');

Route::get('/', ShowDashboard::class)->name('dashboard')->middleware('auth');
