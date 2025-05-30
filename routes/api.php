<?php

use App\Actions\Auth\GetBiometricAuthOptions;
use App\Actions\Auth\HandleWebauthnLogin;
use App\Actions\Dashboard\OnboardCreateVault;
use App\Actions\Dashboard\OnboardSetPassword;
use App\Actions\Dashboard\OnboardSubmit;
use App\Actions\Dashboard\ShowDashboardRecords;
use App\Actions\Fields\GetCategories;
use App\Actions\Fields\GetFields;
use App\Actions\Groups\ShowFavourites;
use App\Actions\Settings\GetWebAuthnConfig;
use App\Actions\Settings\HandleWebAuthnRegistration;
use App\Actions\Vaults\DeleteVault;
use App\Actions\Vaults\GetVaults;
use App\Actions\Vaults\ShowVault;
use App\Actions\Vaults\StoreVault;
use App\Actions\Vaults\UpdateVault;
use App\Actions\Vaults\VaultRecords\DeleteVaultRecord;
use App\Actions\Vaults\VaultRecords\GetVaultRecords;
use App\Actions\Vaults\VaultRecords\ShowVaultRecord;
use App\Actions\Vaults\VaultRecords\StoreVaultRecord;
use App\Actions\Vaults\VaultRecords\UpdateVaultRecord;
use App\Actions\Vaults\VaultRecords\VaultRecordTags\DeleteVaultRecordTag;
use App\Actions\Vaults\VaultRecords\VaultRecordTags\StoreVaultRecordTag;
use App\Actions\Vaults\VaultRecords\VaultRecordValues\ShowVaultRecordValue;
use App\Actions\Vaults\VaultRecords\VaultRecordValues\StoreVaultRecordValue;
use App\Actions\Vaults\VaultRecords\VaultRecordValues\UpdateVaultRecordValue;
use App\Http\Middleware\AssertPKeyMiddleware;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::post('webauthn/get-auth-options', GetBiometricAuthOptions::class);
    Route::post('webauthn/login', HandleWebauthnLogin::class);
});

Route::middleware('auth:api')->group(function () {
    Route::get('user', function () {
        JsonResource::withoutWrapping();

        return UserResource::make(Auth::user());
    });
    Route::get('categories', GetCategories::class);
    Route::get('fields', GetFields::class);

    Route::middleware(AssertPKeyMiddleware::class)->group(function () {
        Route::prefix('onboard')->group(function () {
            Route::post('', OnboardSubmit::class);
            Route::post('password', OnboardSetPassword::class);
            Route::post('vault', OnboardCreateVault::class);
        });

        Route::prefix('vaults')->as('vaults.')->group(function () {
            Route::get('', GetVaults::class)->name('index');
            Route::post('', StoreVault::class)->name('store');

            Route::prefix('{vault}')->group(function () {
                Route::get('', ShowVault::class)->name('show');
                Route::put('', UpdateVault::class)->name('update');
                Route::delete('', DeleteVault::class)->name('destroy');

                Route::prefix('records')->as('records.')->group(function () {
                    Route::get('', GetVaultRecords::class)->name('index');
                    Route::post('', StoreVaultRecord::class)->name('store');
                    Route::get('{record}', ShowVaultRecord::class)->name('show');
                    Route::put('{record}', UpdateVaultRecord::class)->name('update');
                    Route::delete('{record}', DeleteVaultRecord::class)->name('delete');

                    Route::prefix('{record}/values')->as('values.')->group(function () {
                        Route::post('', StoreVaultRecordValue::class)->name('store');

                        Route::get('{value}', ShowVaultRecordValue::class)->name('show');
                        Route::put('{value}', UpdateVaultRecordValue::class)->name('update');
                        Route::delete('{value}', StoreVaultRecordValue::class)->name('destroy');
                    });

                    Route::prefix('{record}/tags')->as('tags.')->group(function () {
                        Route::post('', StoreVaultRecordTag::class)->name('store');
                        Route::delete('{tag}', DeleteVaultRecordTag::class)->name('destroy');
                    });
                });
            });
        });
        Route::get('dashboard', ShowDashboardRecords::class)->name('dashboard');
        Route::get('favourites', ShowFavourites::class)->name('favourites');
    });

    Route::get('webauthn/get-authn-config', GetWebAuthnConfig::class);
    Route::post('webauthn/register-authn', HandleWebAuthnRegistration::class);
});
