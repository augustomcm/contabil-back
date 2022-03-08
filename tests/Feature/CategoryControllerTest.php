<?php

namespace Tests\Feature;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\EntryResource;
use App\Models\Category;
use App\Models\EntryType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_get_all_categories()
    {
        Sanctum::actingAs(
            $this->user
        );

        $incomeCategories = Category::factory(2)->income()->create(['owner_id' => $this->user->id]);
        $expenseCategories = Category::factory(3)->create(['owner_id' => $this->user->id]);
        $categories = $incomeCategories->merge($expenseCategories);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200);

        $response
            ->assertStatus(200)
            ->assertJsonCount( 5, 'data')
            ->assertResource(CategoryResource::collection($categories));
    }


    public function test_get_expense_categories()
    {
        Sanctum::actingAs(
            $this->user
        );

        Category::factory(2)->income()->create(['owner_id' => $this->user->id]);
        $categories = Category::factory(3)->create(['owner_id' => $this->user->id]);

        $response = $this->getJson('/api/categories?type=' . EntryType::EXPENSE->value);

        $response->assertStatus(200);

        $response
            ->assertStatus(200)
            ->assertJsonCount( 3, 'data')
            ->assertResource(CategoryResource::collection($categories));
    }

    public function test_get_income_categories()
    {
        Sanctum::actingAs(
            $this->user
        );

        Category::factory(2)->create(['owner_id' => $this->user->id]);
        $categories = Category::factory(3)->income()->create(['owner_id' => $this->user->id]);

        $response = $this->getJson('/api/categories?type=' . EntryType::INCOME->value);

        $response->assertStatus(200);

        $response
            ->assertStatus(200)
            ->assertJsonCount( 3, 'data')
            ->assertResource(CategoryResource::collection($categories));
    }
}
