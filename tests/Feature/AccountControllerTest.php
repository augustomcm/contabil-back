<?php

namespace Tests\Feature;

use App\Http\Resources\AccountResource;
use App\Models\AccountDefault;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_retrive_owner_accounts_only()
    {
        $accountOfOtherOwner = AccountDefault::factory()->create();

        AccountDefault::factory(5)->create([
            'owner_id' => $this->user->id
        ]);

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->getJson('/api/accounts');
        $response
            ->assertStatus(200)
            ->assertJsonCount( 5, 'data')
            ->assertJsonMissing([
                'id' => $accountOfOtherOwner->id
            ]);
    }

    public function test_create_account()
    {
        Sanctum::actingAs(
            $this->user
        );

        $response = $this->postJson('/api/accounts', [
            'description' => 'Wallet',
            'balance' => 100.00
        ]);

        $response
            ->assertCreated();
    }
}
