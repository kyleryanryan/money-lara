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
        $moneyValues = $command->getMoneyValues();

        $total = Money::total($moneyValues);
        $lowest = Money::lowest($moneyValues);
        $highest = Money::highest($moneyValues);
        $average = Money::average($moneyValues);

        return new MoneyStatisticsResponse([
            'total' => $total,
            'lowest' => $lowest,
            'highest' => $highest,
            'average' => $average,
        ]);
    }
}
