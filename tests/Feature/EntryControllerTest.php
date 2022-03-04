<?php

namespace Tests\Feature;

use App\Helpers\Money;
use App\Http\Resources\EntryResource;
use App\Models\AccountDefault;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EntryControllerTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_pay_entry()
    {
        $entry = Entry::factory()->create([
            'value' => new Money(1000),
            'owner_id' => $this->user->id
        ]);

        $account = AccountDefault::factory()->create([
            'balance' => new Money(1000),
            'owner_id' => $this->user->id
        ]);

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->putJson("/api/entries/{$entry->id}/pay", [
            'account' => $account->id,
            'date' => now()->format('Y-m-d')
        ]);

        $response->assertNoContent();
        $this->assertTrue($entry->refresh()->isPaid());
    }

    public function test_cancel_pay_entry()
    {
        $entry = Entry::factory()
            ->withPayment()
            ->create([
                'value' => new Money(1000),
                'owner_id' => $this->user->id
            ]);

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->putJson("/api/entries/{$entry->id}/cancel-payment");

        $response->assertNoContent();
        $this->assertFalse($entry->fresh()->isPaid());
    }
}
