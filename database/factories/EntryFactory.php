<?php

namespace Database\Factories;

use App\Helpers\Money;
use App\Models\Entry;
use App\Models\EntryType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entry>
 */
class EntryFactory extends Factory
{
    protected $model = Entry::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'value' => new Money(10000),
            'type' => EntryType::DEFAULT
        ];
    }
}
