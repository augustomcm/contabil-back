<?php

namespace Database\Factories;

use App\Helpers\Money;
use App\Models\AccountDefault;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccountDefault>
 */
class AccountDefaultFactory extends Factory
{
    protected $model = AccountDefault::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'balance' => new Money(10000),
            'owner_id' => User::factory()
        ];
    }
}
