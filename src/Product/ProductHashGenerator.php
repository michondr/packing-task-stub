<?php

declare(strict_types=1);

namespace App\Product;

class ProductHashGenerator
{
    public static function generate(array $products): string
    {
        usort(
            $products,
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
                    $products,
                ),
            ),
        );
    }
}
