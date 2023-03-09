<?php

declare(strict_types=1);

namespace App\Product;

readonly class ProductSerializer
{
    public function deserialize(string $json): ProductList
    {
        return new ProductList(
            array_map(
                fn(array $productAsArray) => $this->deserializeSingleProduct($productAsArray),
                json_decode($json, true)['products'],
            )
        );
    }

    private function deserializeSingleProduct(mixed $productAsArray): Product
    {
        return new Product(
            $productAsArray['id'],
            $productAsArray['width'],
            $productAsArray['height'],
            $productAsArray['length'],
            $productAsArray['weight'],
        );
    }
}
