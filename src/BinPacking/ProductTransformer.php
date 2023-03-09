<?php

declare(strict_types=1);

namespace App\BinPacking;

use App\Product\Product;

class ProductTransformer
{
    public function transformProductsToItems(array $products): array
    {
        return array_map(
            fn(Product $p) => $this->transformSingleProductToItem($p),
            $products,
        );
    }

    /**
     * @return array<string, int|float>
     */
    private function transformSingleProductToItem(Product $product): array
    {
        return [
            'id' => $product->getId(),
            'w' => $product->getWidth(),
            'h' => $product->getHeight(),
            'd' => $product->getLength(),
            'wg' => $product->getWeight(),
            'q' => '1',
            'vr' => '1',  //always can be rotated vertically
        ];
    }

}
