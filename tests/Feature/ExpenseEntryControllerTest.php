<?php

namespace Tests\Feature;

use App\Models\Category;
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
        $this->category = Category::factory()->create(['owner_id' => $this->user->id]);
    }

    public function test_create_expense_entry()
    {
        Sanctum::actingAs(
            $this->user
        );

        $value = $this->faker->randomFloat(2);
        $response = $this->postJson('/api/expenses', [
            'description' => $this->faker->text,
            'value' => $value,
            'category_id' => $this->category->id,
            'date' => now()->format('Y-m-d')
        ]);

        $response
            ->assertCreated()
            ->assertJson([
                'value' => $value,
                'paymentType' => EntryPaymentType::DEFAULT->value
            ]);
    }

    public function test_create_expense_entry_credit_card()
    {
        Sanctum::actingAs(
            $this->user
        );

        $creditCard = CreditCard::factory()->create([
            'owner_id' => $this->user->id
        ]);

        $value = $this->faker->randomFloat(2,0, $creditCard->getLimit()->getAmountFloat());
        $response = $this->postJson('/api/expenses', [
            'description' => $this->faker->text,
            'value' => $value,
            'category_id' => $this->category->id,
            'credit_card_id' => $creditCard->id,
            'date' => now()->format('Y-m-d')
        ]);

        $response
            ->assertCreated()
            ->assertJson([
                'value' => $value,
                'paymentType' => EntryPaymentType::CREDIT_CARD->value
            ]);
    }
}
