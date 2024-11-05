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
        $total = $moneyValues->reduce(fn($carry, $money) => $carry->add($money), Money::fromInt(0, $currency));
        $lowest = $moneyValues->min(fn($money) => $money->getAmount());
        $highest = $moneyValues->max(fn($money) => $money->getAmount());

        $average = $total->divide(Money::fromFloat(count($moneyValues), $currency));

        
        return new MoneyStatisticsResponse([
            'total' => $total,
            'lowest' => Money::fromInt($lowest, $currency),
            'highest' => Money::fromInt($highest, $currency),
            'average' => $average,
        ]);
    }
}
