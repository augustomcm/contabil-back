<?php

namespace App\Helpers;

use App\Helpers\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class MoneyCast implements CastsAttributes
{

    public function get($model, string $key, $value, array $attributes)
    {
        return new Money(
            $attributes["{$key}_value"],
            $attributes["{$key}_currency"]
        );
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if (!$value instanceof Money) {
            throw new \InvalidArgumentException("The given $key is not an Money instance.");
        }

        return [
            "{$key}_value" => $value->getAmount(),
            "{$key}_currency" => $value->getCurrencyCode(),
        ];
    }
}
