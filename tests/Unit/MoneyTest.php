<?php

namespace Tests\Unit;

use App\Helpers\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test_create_with_float($value, $expected)
    {
        $money = Money::createByFloat($value);

        $this->assertEquals(
            $money->getAmount(), $expected
        );
    }

    public function dataProvider()
    {
        return [
            [158.48, 15848],
            [158.27, 15827]
        ];
    }
}
