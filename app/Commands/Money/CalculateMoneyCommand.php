<?php

namespace App\Commands\Money;

class CalculateMoneyCommand
{
    public function __construct(private array $moneyValues){
    }

    public function getMoneyValues(): array
    {
        return $this->moneyValues;
    }
}
