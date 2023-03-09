<?php

namespace App;

use App\Entity\Packaging\Packaging;
use App\Facade\ApplicationFacade;
use App\Facade\Exception\ProductsCouldNotBePackedToASinglePackagingException;
use App\Facade\Exception\SomeProductsExceedPackagingValuesException;
use App\Facade\Exception\UnexpectedApiResponseReturnCodeException;
use App\Product\ProductSerializer;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

readonly class Application
{

    public function __construct(
        private ApplicationFacade $applicationFacade,
        private ProductSerializer $productSerializer,
    ) {
    }

    public function run(RequestInterface $request): ResponseInterface
    {
        try {
            $products = $this->productSerializer->deserialize(
                $request->getBody()->getContents(),
            );
        } catch (\Throwable $e) {
            return $this->createBadRequestResponse('invalid products json schema');
        }

        try {
            $selectedPackage = $this->applicationFacade->getPackagingForProducts($products);

            return $this->createSuccessResponse($selectedPackage);
        } catch (UnexpectedApiResponseReturnCodeException $e) {
            return $this->createBadRequestResponse('api is down'); //TODO: do fallback
        } catch (SomeProductsExceedPackagingValuesException $e) {
            return $this->createBadRequestResponse('some products exceed packaging values');
        } catch (ProductsCouldNotBePackedToASinglePackagingException $e) {
            //TODO: what if its more optimal to have two smaller boxes (returned by the api) but you have a larger one that could contain all?
            return $this->createBadRequestResponse('not all products could fit single packaging');
        }
    }


    private function createBadRequestResponse(string $reason): ResponseInterface
    {
        return new Response(400, [], $reason);
    }

    private function createSuccessResponse(Packaging $selectedPackaging): ResponseInterface
    {
        return new Response(
            200,
            [],
            sprintf(
                'Smallest box that we can use for products (%.2f, %.2f, %.2f).',
                $selectedPackaging->getWidth(),
                $selectedPackaging->getHeight(),
                $selectedPackaging->getLength(),
            )
        );
    }

}
