<?php

namespace App\Actions\Auth;

use App\Models\Authorization;
use App\Models\User;
use App\Models\VaultRecordValue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Token;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use RuntimeException;

class ChangePassword
{
    use AsAction;

    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'password' => 'required|string|confirmed|min:8',
        ];
    }

    public function handle(ActionRequest $request): void
    {
        /** @var User $user */
        $user = Auth::user();

        if (!Hash::check($request->get('current_password'), $user->password)) {
            $validator = Validator::make($request->all(), $this->rules());
            $validator->errors()->add('current_password', 'Password invalid');
            throw new ValidationException($validator);
        }

        if (!blink()->get('pkey')) {
            throw new RuntimeException('Problem trying to reset your password. Please try again later');
        }

        DB::transaction(function () use($user, $request) {
            MigrateVaults::make()->handle($user, $request->get('password'));

            // Remove all authorizations (webauth)
            Authorization::query()
                ->where('user_id', '=', $user->id)
                ->delete();

            // Revoke all tokens
            Token::query()
                ->where('user_id', '=', $user->id)
                ->update([
                    'revoked' => true,
                ]);
        });
    }
}
