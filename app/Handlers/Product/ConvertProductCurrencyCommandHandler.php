<?php

namespace App\Handlers\Product;

use App\Models\Product;
use App\Commands\Product\ConvertProductCurrencyCommand;
use App\Http\Responses\Product\ConvertProductCurrencyResponse;
use App\Services\Money\Money;
use InvalidArgumentException;

class ConvertProductCurrencyCommandHandler
{

    /**
     * Summary of handle
     * 
     * @param ConvertProductCurrencyCommand $command
     * @throws \InvalidArgumentException
     * @return ConvertProductCurrencyResponse
     */
    public function __invoke(ConvertProductCurrencyCommand $command): ConvertProductCurrencyResponse
    {
        $product = Product::findOrFail($command->getProductId());

        /** @var Money $money */
        $money = $product->getMoney();

        if (!$money) {
            throw new InvalidArgumentException('Product money value not found.');
        }

        $targetCurrency = $command->getTargetCurrency();
        $convertedMoney = $money->convert($targetCurrency);

        $product->price = $convertedMoney->getAmount();
        $product->currency = $convertedMoney->getCurrency()->value;
        $product->save();

        $resourceData = [
            'original_price' => $money,
            'converted_price' => $convertedMoney,
        ];

        return new ConvertProductCurrencyResponse($resourceData);
    }
}
