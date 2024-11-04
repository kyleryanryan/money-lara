<?php

namespace App\Handlers\Product;

use App\Commands\Product\CreateProductCommand;
use App\Http\Responses\CreateProductResponse;
use App\Models\Product;
use App\Services\Money\Money;

class CreateProductCommandHandler
{
    public function handle(CreateProductCommand $command): CreateProductResponse
    {
        $money = new Money($command->getPriceInSmallestUnit(), $command->getCurrency());

        $product = Product::create([
            'name' => $command->getName(),
            'price' => $money->getAmount(),
            'currency' => $money->getCurrency()->value,
        ]);

        return new CreateProductResponse([
            'product' => $product,
            'money' => $money,
        ]);
    }
}
