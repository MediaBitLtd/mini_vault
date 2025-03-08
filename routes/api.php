<?php

use App\Actions\Auth\GetBiometricAuthOptions;
use App\Actions\Auth\HandleWebauthnLogin;
use App\Actions\Groups\ShowFavourites;
use App\Actions\Settings\GetWebAuthnConfig;
use App\Actions\Settings\HandleWebAuthnRegistration;
use App\Actions\Vaults\CreateVault;
use App\Actions\Vaults\DeleteVault;
use App\Actions\Vaults\GetVaults;
use App\Actions\Vaults\ShowVault;
use App\Actions\Vaults\VaultRecords\CreateVaultRecord;
use App\Actions\Vaults\VaultRecords\DeleteVaultRecord;
use App\Actions\Vaults\VaultRecords\GetVaultRecords;
use App\Actions\Vaults\VaultRecords\ShowVaultRecord;
use App\Actions\Vaults\VaultRecords\UpdateVaultRecord;
use App\Actions\Vaults\VaultRecords\VaultRecordValues\ShowVaultRecordValue;
use App\Actions\Vaults\VaultRecords\VaultRecordValues\StoreVaultRecordValue;
use App\Actions\Vaults\VaultRecords\VaultRecordValues\UpdateVaultRecordValue;
use App\Http\Middleware\AssertPKeyMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::post('webauthn/get-auth-options', GetBiometricAuthOptions::class);
    Route::post('webauthn/login', HandleWebauthnLogin::class);
});

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
                    Route::put('{record}', UpdateVaultRecord::class)->name('update');
                    Route::delete('{record}', DeleteVaultRecord::class)->name('delete');

                    Route::post('{record}/values', StoreVaultRecordValue::class)->name('values.store');

                    Route::get('{record}/values/{value}', ShowVaultRecordValue::class)->name('values.show');
                    Route::put('{record}/values/{value}', UpdateVaultRecordValue::class)->name('values.update');
                    Route::delete('{record}/values/{value}', StoreVaultRecordValue::class)->name('values.destroy');
                });
            });
        });
        Route::get('favourites', ShowFavourites::class)->name('favourites');
    });

    Route::get('webauthn/get-authn-config', GetWebAuthnConfig::class);
    Route::post('webauthn/register-authn', HandleWebAuthnRegistration::class);
});
