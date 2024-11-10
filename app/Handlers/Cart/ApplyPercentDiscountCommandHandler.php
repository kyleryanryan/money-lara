<?php

namespace App\Handlers\Cart;

use App\Commands\Cart\ApplyPercentDiscountCommand;
use App\Enums\Currency;
use App\Http\Responses\DiscountedCartResponse;
use App\Models\Product;
use App\Services\Money\Money;
use InvalidArgumentException;

class ApplyPercentDiscountCommandHandler
{

    public function __invoke(ApplyPercentDiscountCommand $command): DiscountedCartResponse
    {
        $productIds = $command->getProductIds();
        $discountPercentage = $command->getDiscountPercentage();

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

        $discountedTotalMoney = $totalMoney->discount($discountPercentage);

        $responseData = [
            'products' => $productList,
            'subTotal' => $totalMoney,
            'total' => $discountedTotalMoney,
            'discount' => $discountPercentage . '%',
        ];

        return new DiscountedCartResponse($responseData);
    }
}
