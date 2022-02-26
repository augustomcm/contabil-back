<?php

namespace Tests\Feature;

use App\Models\CreditCard;
use App\Models\EntryPaymentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExpenseEntryControllerTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_create_expense_entry()
    {
        Sanctum::actingAs(
            $this->user
        );

        $response = $this->postJson('/api/expenses', [
            'value' => 20.5
        ]);

        $response
            ->assertCreated()
            ->assertJson([
                'payment_type' => EntryPaymentType::DEFAULT->value
            ]);
    }

    public function test_create_expense_entry_redit_card()
    {
        Sanctum::actingAs(
            $this->user
        );

        $creditCard = CreditCard::factory()->create([
            'owner_id' => $this->user->id
        ]);

        $response = $this->postJson('/api/expenses', [
            'value' => 53.51,
            'credit_card_id' => $creditCard->id
        ]);

        $response
            ->assertCreated()
            ->assertJson([
                'payment_type' => EntryPaymentType::CREDIT_CARD->value
            ]);
    }
}
