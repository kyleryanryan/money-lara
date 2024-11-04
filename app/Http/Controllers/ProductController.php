<?php

namespace App\Http\Controllers;

use App\Commands\Product\ConvertProductCurrencyCommand;
use App\Commands\Product\CreateProductCommand;
use App\Services\Money\Money;
use App\Enums\Currency;
use App\Handlers\Product\ConvertProductCurrencyCommandHandler;
use App\Handlers\Product\CreateProductCommandHandler;
use App\Http\Requests\ConvertCurrencyRequest;
use App\Http\Requests\CreateProductRequest;
use App\Http\Responses\CreateProductResponse;
use App\Http\Responses\Product\ConvertProductCurrencyResponse;
use App\Support\NumberHelper;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends Controller
{
    /**
     * Method for storing the money object in the database
     * 
     * @param CreateProductRequest $request
     * @param CreateProductCommandHandler $handler
     * @return CreateProductResponse
     */
    public function store(CreateProductRequest $request, CreateProductCommandHandler $handler): CreateProductResponse
    {
        $priceInSmallestUnit = NumberHelper::floatToInt($request->input('price'), Money::STORAGE_PRECISION);
        $currency = Currency::from($request->input('currency'));

        $command = new CreateProductCommand(
            name: $request->input('name'),
            priceInSmallestUnit: $priceInSmallestUnit,
            currency: $currency
        );

        return $handler->handle($command);
    }

    /**
     * Money object to a different currency
     * 
     * @param ConvertCurrencyRequest $request
     * @param ConvertProductCurrencyCommandHandler $handler
     * @param string $id
     * @return ConvertProductCurrencyResponse|JsonResponse
     */
    public function convertPrice(ConvertCurrencyRequest $request, ConvertProductCurrencyCommandHandler $handler, string $id): ConvertProductCurrencyResponse|JsonResponse
    {
        $targetCurrency = $request->getTargetCurrency();
        $command = new ConvertProductCurrencyCommand($id, $targetCurrency);

        try {
            return $handler->handle($command);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => 'Conversion rate not available.'], 400);
        }
    }
}
