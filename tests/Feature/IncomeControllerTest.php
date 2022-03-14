<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\EntryPaymentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IncomeControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->category = Category::factory()->income()->create(['owner_id' => $this->user->id]);
    }

    public function test_create_income_entry()
    {
        Sanctum::actingAs(
            $this->user
        );

        $value = $this->faker->randomFloat(2);
        $response = $this->postJson('/api/incomes', [
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
}
