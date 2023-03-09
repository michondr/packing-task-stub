<?php

declare(strict_types=1);

namespace App\BinPacking;

use App\Entity\PackagedProducts\PackagedProducts;
use App\Entity\Packaging\Packaging;
use Doctrine\ORM\EntityManager;
use Psr\SimpleCache\CacheInterface;

class PackingCache implements CacheInterface
{

    public function __construct(
        private EntityManager $entityManager
    )
    {
    }

    public function get(string $key, mixed $default = null): ?PackagedProducts
    {
        assert(is_string($key));

        return $this->entityManager->getRepository(PackagedProducts::class)->find($key);
    }

    public function set(string $key, mixed $value, \DateInterval|int|null $ttl = null): bool
    {
        assert(is_string($key));
        assert($value instanceof Packaging);

        $packagedProducts = new PackagedProducts(
            $key,
            $value
        );

        $this->entityManager->persist($packagedProducts);
        $this->entityManager->flush();

        return true;
    }

    public function delete(string $key): bool
    {
        throw new \Exception('not necessary at this moment');
    }

    public function clear(): bool
    {
        throw new \Exception('not necessary at this moment');
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        throw new \Exception('not necessary at this moment');
    }

    public function setMultiple(iterable $values, \DateInterval|int|null $ttl = null): bool
    {
        throw new \Exception('not necessary at this moment');
    }

    public function deleteMultiple(iterable $keys): bool
    {
        throw new \Exception('not necessary at this moment');
    }

    public function has(string $key): bool
    {
        throw new \Exception('not necessary at this moment');
    }
}
