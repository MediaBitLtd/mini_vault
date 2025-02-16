<?php

use App\Actions\Vaults\CreateVault;
use App\Actions\Vaults\GetVaults;
use App\Actions\Vaults\ShowVault;
use App\Http\Middleware\AssertPKeyMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::middleware(AssertPKeyMiddleware::class)->group(function () {
        Route::get('vaults', GetVaults::class)->name('vaults.index');
        Route::post('vaults', CreateVault::class)->name('vaults.store');
        Route::get('vaults/{vault}', ShowVault::class)->name('vaults.show');
    });
});
