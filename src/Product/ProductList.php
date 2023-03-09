<?php

declare(strict_types=1);

namespace App\Product;

readonly class ProductList
{
    /**
     * @param array<Product> $products
     */
    public function __construct(
        private array $products,
    ) {
    }

    /**
     * @return array<Product>
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    public function getHashKey(): string
    {
        $productsOrderedById = $this->products;

        usort(
            $productsOrderedById,
            fn(Product $a, Product $b) => $a->getId() <=> $b->getId(),
        );

        return md5(
            implode(
                ',',
                array_map(
                    fn(Product $p) => sprintf(
                        'width:%f,height:%f,length:%f,weight:%f',
                        $p->getWidth(),
                        $p->getHeight(),
                        $p->getLength(),
                        $p->getWeight(),
                    ),
                    $productsOrderedById,
                ),
            ),
        );
    }
}
