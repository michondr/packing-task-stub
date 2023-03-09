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
        private PackingCache $packingCache
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
            var_dump('returning from cache'); //TODO: log cache hit
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
        print_r($packagingResponseData); //TODO: log this

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
            $selectedPackaging
        );

        return $selectedPackaging;
    }


}
