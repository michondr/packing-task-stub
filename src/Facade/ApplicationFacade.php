<?php

declare(strict_types=1);

namespace App\Facade;

use App\BinPacking\ProductTransformer;
use App\BinPacking\PackagingTransformer;
use App\BinPacking\PackingCache;
use App\BinPacking\PackingClient;
use App\Entity\Packaging\Packaging;
use App\Entity\Packaging\PackagingRepository;
use App\Facade\Exception\ProductsCouldNotBePackedToASinglePackagingException;
use App\Facade\Exception\SomeProductsExceedPackagingValuesException;
use App\Facade\Exception\UnexpectedApiResponseReturnCodeException;
use App\Product\ProductList;

readonly class ApplicationFacade
{
    public function __construct(
        private PackagingRepository $packagingRepository,
        private PackagingTransformer $packagingTransformer,
        private ProductTransformer $itemsTransformer,
        private PackingClient $packingClient,
        private PackingCache $packingCache,
    ) {
    }

    /**
     * @throws UnexpectedApiResponseReturnCodeException
     * @throws SomeProductsExceedPackagingValuesException
     * @throws ProductsCouldNotBePackedToASinglePackagingException
     */
    public function getPackagingForProducts(ProductList $products): Packaging
    {
        $cachedPackedProduct = $this->packingCache->get($products->getHashKey());

        if ($cachedPackedProduct !== null) {
            var_dump('returning from cache'); //TODO: use logger->info for cache hit
            return $cachedPackedProduct->getAvailablePackaging();
        }

        $allPackaging = $this->packagingRepository->findAll();

        $packingResponse = $this->packingClient->getPackingInformation(
            $this->packagingTransformer->transformPackagingToBins($allPackaging),
            $this->itemsTransformer->transformProductsToItems($products),
        );

        if ($packingResponse->getStatusCode() !== 200) {
            throw new UnexpectedApiResponseReturnCodeException();
        }

        $packagingResponseData = json_decode($packingResponse->getBody()->getContents(), true);
        print_r($packagingResponseData); //TODO: use logger->info for endpoint response hit

        $notPackedItems = $packagingResponseData['response']['not_packed_items'];
        $binsPacked = $packagingResponseData['response']['bins_packed'];

        if (count($notPackedItems) > 0) {
            throw new SomeProductsExceedPackagingValuesException();
        }
        if (count($binsPacked) > 1) {
            throw new ProductsCouldNotBePackedToASinglePackagingException();
        }

        $selectedPackagingId = $binsPacked[0]['bin_data']['id'];
        $selectedPackaging = $this->packagingRepository->getById($selectedPackagingId);

        $this->packingCache->set(
            $products->getHashKey(),
            $selectedPackaging,
        );

        return $selectedPackaging;
    }

    /**
     * @throws ProductsCouldNotBePackedToASinglePackagingException
     */
    public function getFallbackPackaging(ProductList $productList): Packaging
    {
        $allPackaging = $this->packagingRepository->findAll();

        usort(
            $allPackaging,
            fn(Packaging $a, Packaging $b) => $a->getVolume() <=> $b->getVolume(),
        );

        foreach ($allPackaging as $availablePackaging) {
            //TODO: move volume safety constant to config
            if (
                $availablePackaging->getMaxWeight() > $productList->getTotalWeight()
                && $availablePackaging->getVolume() > (1.25 * $productList->getTotalVolume())
            ) {
                $noProductExceedsPackagingDimension = true;

                foreach ($productList->getProducts() as $product) {
                    if (
                        $availablePackaging->getWidth() < $product->getWidth()
                        || $availablePackaging->getHeight() < $product->getHeight()
                        || $availablePackaging->getLength() < $product->getLength()
                    ) {
                        $noProductExceedsPackagingDimension = false;
                    }
                }

                if ($noProductExceedsPackagingDimension === true) {
                    return $availablePackaging;
                }
            }
        }

        throw new ProductsCouldNotBePackedToASinglePackagingException();
    }


}
