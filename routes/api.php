<?php

use App\Actions\Vaults\CreateVault;
use App\Actions\Vaults\DeleteVault;
use App\Actions\Vaults\GetVaults;
use App\Actions\Vaults\ShowVault;
use App\Actions\Vaults\VaultRecords\CreateVaultRecord;
use App\Actions\Vaults\VaultRecords\ShowVaultRecord;
use App\Actions\Vaults\VaultRecords\GetVaultRecords;
use App\Actions\Vaults\VaultRecords\ShowVaultRecordValue;
use App\Actions\Vaults\VaultRecords\UpdateVaultRecordValue;
use App\Http\Middleware\AssertPKeyMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::middleware(AssertPKeyMiddleware::class)->group(function () {
        Route::prefix('vaults')->as('vaults.')->group(function () {
            Route::get('', GetVaults::class)->name('index');
            Route::post('', CreateVault::class)->name('store');

            Route::prefix('{vault}')->group(function () {
                Route::get('', ShowVault::class)->name('show');
                Route::delete('', DeleteVault::class)->name('destroy');

                Route::prefix('records')->as('records.')->group(function () {
                    Route::get('', GetVaultRecords::class)->name('index');
                    Route::post('', CreateVaultRecord::class)->name('store');
                    Route::get('{record}', ShowVaultRecord::class)->name('show');

                    Route::get('{record}/values/{value}', ShowVaultRecordValue::class)->name('values.show');
                    Route::put('{record}/values/{value}', UpdateVaultRecordValue::class)->name('values.update');
                });
            });
        });
    });
});
