<?php

declare(strict_types=1);

namespace App\Product;

class ProductSerializer
{
    /**
     * @param string $json
     *
     * @return Product[]
     */
    public function deserialize(string $json): array
    {
        return array_map(
            fn(array $productAsArray) => $this->deserializeSingleProduct($productAsArray),
            json_decode($json, true)['products'],
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
