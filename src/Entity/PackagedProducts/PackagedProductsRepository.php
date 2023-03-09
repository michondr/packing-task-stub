<?php

declare(strict_types=1);

namespace App\Entity\PackagedProducts;

use App\Entity\Packaging\Packaging;
use App\Product\Product;
use App\Product\ProductHashGenerator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;

class PackagedProductsRepository
{
    public function __construct(
        private EntityManager $entityManager,
    ) {
    }

    public function getByProducts(array $products)
    {
        return $this->entityManager
            ->getRepository(PackagedProducts::class)
            ->find(ProductHashGenerator::generate($products));
    }

    /**
     * @param array<Product> $products
     * @param Packaging $packaging
     */
    public function savePackagingForProducts(array $products, Packaging $packaging): PackagedProducts
    {
        $packagedProducts = new PackagedProducts(
            ProductHashGenerator::generate($products),
            $packaging
        );

        $this->entityManager->persist($packagedProducts);
        $this->entityManager->flush();

        return $packagedProducts;
    }
}
