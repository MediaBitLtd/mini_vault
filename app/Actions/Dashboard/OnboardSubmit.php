<?php

namespace App\Actions\Dashboard;

use App\Models\User;
use App\Traits\Resources;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

class OnboardSubmit
{
    use AsAction;
    use Resources;

    public function handle(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->vaults()->doesntExist()) {
            return false;
        }

        $user->onboard = true;

        return $user->save();
    }

    public function jsonResponse(bool $success): JsonResponse
    {
        if ($success) {
            return $this->sendSuccessfulResponse();
        }

        return new JsonResponse([
            'success' => false,
        ], 400);
    }
}
