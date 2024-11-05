<?php

namespace App\Handlers\Money;

use App\Commands\Money\CalculateMoneyCommand;
use App\Http\Responses\MoneyStatisticsResponse;
use App\Services\Money\Money;
use App\Support\NumberHelper;

class CalculateMoneyCommandHandler
{
    public function __invoke(CalculateMoneyCommand $command): MoneyStatisticsResponse
    {
        $moneyValues = collect($command->getMoneyValues());

        $currency = $moneyValues->first()->getCurrency();
        $total = $moneyValues->reduce(fn($carry, $money) => $carry->add($money), new Money(0, $currency));
        $lowest = $moneyValues->min(fn($money) => $money->getAmount());
        $highest = $moneyValues->max(fn($money) => $money->getAmount());

        $divisorInSmallestUnit = NumberHelper::floatToInt(count($moneyValues), Money::STORAGE_PRECISION);
        $average = $total->divide(new Money($divisorInSmallestUnit, $currency));

        return new MoneyStatisticsResponse([
            'total' => $total,
            'lowest' => new Money($lowest, $currency),
            'highest' => new Money($highest, $currency),
            'average' => $average,
        ]);
    }
}
