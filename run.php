<?php

use App\Application;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;

/** @var EntityManager $entityManager */
$entityManager = require __DIR__ . '/src/bootstrap.php';

$request = new Request('POST', new Uri('http://localhost/pack'), ['Content-Type' => 'application/json'], $argv[1]);

$application = new Application(
    new \App\Facade\ApplicationFacade(
        new \App\Entity\Packaging\PackagingRepository($entityManager),
        new \App\BinPacking\PackagingTransformer(),
        new \App\BinPacking\ProductTransformer(),
        new \App\BinPacking\PackingClient(
            new \GuzzleHttp\Client()
        ),
        new \App\BinPacking\PackingCache($entityManager)
    ),
    new \App\Product\ProductSerializer(),
);
$response = $application->run($request);

echo "<<< In:\n" . Message::toString($request) . "\n\n";
echo ">>> Out:\n" . Message::toString($response) . "\n\n";
