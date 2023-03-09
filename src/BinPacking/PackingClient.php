<?php

declare(strict_types=1);

namespace App\BinPacking;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class PackingClient
{

    public function __construct(
        private Client $guzzleClient,
        private string $apiUser = 'me@michondr.cz',
        private string $apiKey = '9d20d07da050d18200f6787f1d4b8fa1',
        private string $apiUrl = 'https://eu.api.3dbinpacking.com/packer/packIntoMany',
    ) {
    }

    public function getPackingInformation(
        array $serializedPackages,
        array $serializedProducts,
    ) {
        $postBody = [
            'bins' => $serializedPackages,
            'items' => $serializedProducts,
            'username' => $this->apiUser,
            'api_key' => $this->apiKey,
        ];

        //TODO: use logger->info for endpoint request. what to do with the api key?

        return $this->guzzleClient->sendRequest(
            new Request(
                'POST',
                $this->apiUrl,
                [],
                json_encode($postBody)
            ),
        );
    }
}
