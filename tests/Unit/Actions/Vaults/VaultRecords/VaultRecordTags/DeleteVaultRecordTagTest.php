<?php

namespace Actions\Vaults\VaultRecords\VaultRecordTags;

use App\Actions\Vaults\VaultRecords\VaultRecordTags\DeleteVaultRecordTag;
use App\Models\VaultRecord;
use App\Models\VaultRecordTag;
use Tests\TestCase;

class DeleteVaultRecordTagTest extends TestCase
{
    public function test_action()
    {
        [$vault] = $this->getVaultAndUser();

        $record = VaultRecord::factory()->for($vault)->create();

        $tag = $record->tags()->create([
            'name' => 'test',
        ]);

        DeleteVaultRecordTag::make()->handle($vault, $record, $tag);

        $this->assertDatabaseCount(VaultRecordTag::class, 0);
    }
}
