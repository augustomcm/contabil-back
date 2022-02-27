<?php

namespace Tests\Feature;

use App\Http\Resources\EntryResource;
use App\Models\Entry;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EntryControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_retrive_entries()
    {
        $entries = Entry::factory(5)->create([
            'owner_id' => $this->user->id
        ]);

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->getJson('/api/entries');

        $response
            ->assertStatus(200)
            ->assertJsonCount( 5, 'data')
            ->assertResource(EntryResource::collection($entries));
    }

    public function test_retrive_owner_entries_only()
    {
        $entryOfOtherOwner = Entry::factory()->create();

        Entry::factory(5)->create([
            'owner_id' => $this->user->id
        ]);

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->getJson('/api/entries');
        $response
            ->assertStatus(200)
            ->assertJsonCount( 5, 'data')
            ->assertJsonMissing([
                'id' => $entryOfOtherOwner->id
            ]);
    }

    public function test_remove_entry()
    {
        $entry = Entry::factory()->create([
            'owner_id' => $this->user->id
        ]);

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->deleteJson("/api/entries/{$entry->id}");

        $response->assertNoContent();
        $this->assertModelMissing($entry);
    }
}
