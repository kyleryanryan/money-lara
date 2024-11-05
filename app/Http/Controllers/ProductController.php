<?php

namespace App\Http\Controllers;

use App\CommandBus;
use App\Commands\Product\ConvertProductCurrencyCommand;
use App\Commands\Product\CreateProductCommand;
use App\Services\Money\Money;
use App\Enums\Currency;
use App\Handlers\Product\ConvertProductCurrencyCommandHandler;
use App\Http\Requests\ConvertCurrencyRequest;
use App\Http\Requests\CreateProductRequest;
use App\Http\Responses\CreateProductResponse;
use App\Http\Responses\Product\ConvertProductCurrencyResponse;
use App\Support\NumberHelper;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends Controller
{
    public function __construct(private CommandBus $commandBus){}

    /**
     * Method for storing/handling the money object in the database
     * 
     * @param CreateProductRequest $request
     * @return CreateProductResponse
     */
    public function store(CreateProductRequest $request): CreateProductResponse
    {
        $command = new CreateProductCommand(
            name: $request->getName(),
            price: $request->getPrice(),
            currency: $request->getCurrency()
        );

        return $this->commandBus->handle($command);
    }

    /**
     * Money object to a different currency
     * 
     * @param ConvertCurrencyRequest $request
     * @param ConvertProductCurrencyCommandHandler $handler
     * @param string $id
     * @return ConvertProductCurrencyResponse|JsonResponse
     */
    public function convertPrice(ConvertCurrencyRequest $request, string $id): ConvertProductCurrencyResponse|JsonResponse
    {
        $targetCurrency = $request->getTargetCurrency();
        $command = new ConvertProductCurrencyCommand($id, $targetCurrency);

        try {
            return $this->commandBus->handle($command);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => 'Conversion rate not available.'], 400);
        }
    }
}
