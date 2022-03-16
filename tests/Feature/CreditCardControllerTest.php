<?php

namespace Tests\Feature;

use App\Http\Resources\CreditCardResource;
use App\Models\AccountDefault;
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

    public function test_create_credit_card()
    {
        Sanctum::actingAs(
            $this->user
        );

        $response = $this->postJson('/api/credit-cards', [
            'description' => 'Credit Card',
            'closing_day' => 12,
            'expiration_day' => 20,
            'limit' => 100.00
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure(['id']);
    }

    public function test_retrive_credit_cards()
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

    public function test_close_current_invoice()
    {
        $creditCard = CreditCard::factory()->create([
            'closing_day' => now()->day,
            'owner_id' => $this->user->id
        ]);
        $currentInvoice = $creditCard->getCurrentInvoice();
        $currentInvoice->final_date = now()->setDay($creditCard->closing_day)->startOfDay();
        $currentInvoice->save();


        Sanctum::actingAs(
            $this->user
        );

        $response = $this->putJson("/api/credit-cards/{$creditCard->id}/close-invoice");

        $response->assertNoContent();
    }

    public function test_pay_current_invoice()
    {
        $creditCard = CreditCard::factory()->withClosedInvoice()->create([
            'closing_day' => now()->subDay()->day,
            'owner_id' => $this->user->id
        ]);

        $account = AccountDefault::factory()->create([
            'balance' => $creditCard->getLimit(),
            'owner_id' => $this->user->id
        ]);

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->putJson("/api/credit-cards/{$creditCard->id}/pay-invoice", [
            'account' => $account->id
        ]);

        $response->assertNoContent();
    }

    public function test_update_credit_card()
    {
        $creditCard = CreditCard::factory()->withClosedInvoice()->create([
            'closing_day' => now()->subDay()->day,
            'owner_id' => $this->user->id
        ]);

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->putJson('/api/credit-cards/' . $creditCard->id, [
            'description' => 'Credit Card',
            'closing_day' => 12,
            'expiration_day' => 20,
            'limit' => 100.00
        ]);

        $response->assertNoContent();
    }
}
