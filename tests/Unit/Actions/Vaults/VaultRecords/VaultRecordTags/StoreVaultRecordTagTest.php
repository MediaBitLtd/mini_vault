<?php

namespace Actions\Vaults\VaultRecords\VaultRecordTags;

use App\Actions\Vaults\VaultRecords\VaultRecordTags\StoreVaultRecordTag;
use App\Models\VaultRecord;
use App\Models\VaultRecordTag;
use Tests\TestCase;

class StoreVaultRecordTagTest extends TestCase
{
    public function test_action()
    {
        [$vault] = $this->getVaultAndUser();

        $record = VaultRecord::factory()->for($vault)->create();

        $action = StoreVaultRecordTag::make();
        $request = $this->createRequest([
            'name' => 'test',
        ], $action->rules());

        $action->handle($vault, $record, $request);

        $this->assertDatabaseCount(VaultRecordTag::class, 1);
        $this->assertEquals('test', VaultRecordTag::query()->first()->name);
    }
}
