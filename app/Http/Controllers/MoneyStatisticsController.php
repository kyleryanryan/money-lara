<?php

namespace App\Http\Controllers;

use App\CommandBus;
use App\Commands\Money\CalculateMoneyCommand;
use App\Http\Requests\CalculateMoneyStatisticsRequest;
use App\Http\Responses\MoneyStatisticsResponse;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class MoneyStatisticsController extends Controller
{
    public function __construct(private CommandBus $commandBus){}

    public function calculateStatistics(CalculateMoneyStatisticsRequest $request): MoneyStatisticsResponse|JsonResponse
    {
        try{
            $moneyValues = $request->getValidatedMoneyValues();
            $command = new CalculateMoneyCommand($moneyValues);

            return $this->commandBus->handle($command);;
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
