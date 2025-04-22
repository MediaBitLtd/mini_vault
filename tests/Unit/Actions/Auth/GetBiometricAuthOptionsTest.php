<?php

namespace Actions\Auth;

use App\Actions\Auth\GetBiometricAuthOptions;
use App\Models\Authorization;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\ActionRequest;
use Tests\TestCase;

class GetBiometricAuthOptionsTest extends TestCase
{
    public function test_action()
    {
        $user = User::factory()->create([
            'id' => 1000,
            'biometric_key' => 'test_123',
        ]);

        Authorization::factory()->for($user)->count(5)->create();

        $result = GetBiometricAuthOptions::make()->handle(new ActionRequest([
            'userId' => 1000,
            'userKey' => 'test_123',
            'cypher' => encrypt('test_p_key'),
        ]));

        $cacheData = Cache::get('webauthn.auth:1000');

        $this->assertSame('test_p_key', $cacheData['pkey']);
        $this->assertNotEmpty($cacheData['challenge']);

        $this->assertEquals($cacheData['challenge'], $result['challenge']);
        $this->assertCount(5, $result['allowCredentials']);
    }

    public function test_action_throws_if_wrong_user()
    {
        User::factory()->create([
            'id' => 1000,
            'biometric_key' => 'test_123',
        ]);

        try {
            GetBiometricAuthOptions::make()->handle(new ActionRequest([
                'userId' => 1000,
                'userKey' => 'WRONG',
                'pKey' => 'test_p_key',
            ]));
        } catch (AuthorizationException) {
            $this->assertTrue(true);
        }

        $this->assertNull(Cache::get('webauthn.auth:1000'));
    }
}
