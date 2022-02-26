<?php

namespace App\Helpers;
 use App\Helpers\Interfaces\Money as IMoney;
 use Money\Currencies\ISOCurrencies;
 use Money\Currency;
 use Money\Formatter\IntlMoneyFormatter;
 use Money\Money as MoneyDefault;

 class Money implements IMoney
{
     protected $money;
     const CURRENCY = 'BRL';
     const LOCALE = 'pt_BR';

     public function __construct(int|string $value, $currency = self::CURRENCY)
     {
         $this->money = new MoneyDefault($value, new Currency($currency));
     }

     static public function createByFloat(float $value, $currency = self::CURRENCY)
     {
         $number = round($value * 100);
         return new self($number, $currency);
     }

     public function add(IMoney $money) : IMoney
     {
         return $this->createByMoney($this->money->add($money->money));
     }

     public function subtract(IMoney $money) : IMoney
     {
         return $this->createByMoney($this->money->subtract($money->money));
     }

     public function multiply(int|string $value) : IMoney
     {
         return $this->createByMoney($this->money->multiply($value));
     }

     public function absolute() : IMoney
     {
         return $this->createByMoney($this->money->absolute());
     }

     public function negative() : IMoney
     {
         return $this->createByMoney($this->money->negative());
     }

     private function createByMoney(MoneyDefault $money)
     {
         return new Money($money->getAmount(), $money->getCurrency()->getCode());
     }

     public function equals(IMoney $money) :  bool
     {
         return $this->money->equals($money->money);
     }

     public function isNegative() : bool
     {
         return $this->money->isNegative();
     }

     public function allocateTo(int $n) : array
     {
         return array_map(function(MoneyDefault $money) {
             return new Money($money->getAmount());
         }, $this->money->allocateTo($n));
     }

     public function format($absolute = false, $locale = self::LOCALE) : string
     {
         $moneryToFormat = $this->money;

         if($absolute)
             $moneryToFormat = $this->money->absolute();

         $numberFormatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
         $moneyFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies());

         return $moneyFormatter->format($moneryToFormat);
     }

     public function getAmount() : string
     {
         return $this->money->getAmount();
     }

     public function getCurrencyCode() : string
     {
         return $this->money->getCurrency()->getCode();
     }

     public function jsonSerialize()
     {
         return $this->money->jsonSerialize();
     }

     public function greaterThan(IMoney $money): bool
     {
         return $this->money->greaterThan($money->money);
     }

     public function getAmountFloat(): float
     {
         return $this->getAmount() / 100;
     }
 }
