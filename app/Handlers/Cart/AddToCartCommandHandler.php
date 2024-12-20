<?php

namespace App\Handlers\Cart;

use App\Commands\Cart\AddToCartCommand;
use App\Enums\Currency;
use App\Http\Responses\CartResponse;
use App\Models\Product;
use App\Services\Money\Money;
use InvalidArgumentException;

class AddToCartCommandHandler
{
    public function __invoke(AddToCartCommand $command): CartResponse
    {
        $productIds = $command->getProductIds();
        $products = Product::whereIn('id', $productIds)->get();

        if ($products->isEmpty()) {
            throw new InvalidArgumentException('No valid products found.');
        }

        $firstProduct = $products->first();
        $currency = Currency::from($firstProduct->currency);
        $totalMoney = Money::fromScaledInt(0, $currency);

        $productList = [];

        foreach ($products as $product) {
            $productMoney = $product->getMoney();
            $productList[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $productMoney->formatAmountWithSymbol(),
            ];

            $totalMoney = $totalMoney->add($productMoney);
        }

        $responseData = [
            'products' => $productList,
            'total' => $totalMoney,
        ];

        return new CartResponse($responseData);
    }
}
