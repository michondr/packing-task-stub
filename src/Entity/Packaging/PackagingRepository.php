<?php

declare(strict_types=1);

namespace App\Entity\Packaging;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;

class PackagingRepository
{
    public function __construct(
        private EntityManager $entityManager,
    ) {
    }

    /**
     * @return array<Packaging>
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Packaging::class)->findAll();
    }

    public function getById(int $id): Packaging
    {
        $result = $this->entityManager->getRepository(Packaging::class)->find($id);

        if ($result === null) {
            throw new EntityNotFoundException();
        }

        return $result;
    }
}
