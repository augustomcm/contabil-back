<?php

namespace Tests\Feature;

use App\Http\Resources\CreditCardResource;
use App\Models\CreditCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreditCardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_retrive_accounts()
    {
        $creditCards = CreditCard::factory(5)->create([
            'owner_id' => $this->user->id
        ]);

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->getJson('/api/credit-cards');

        $response
            ->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertResource(CreditCardResource::collection($creditCards));
    }
}
