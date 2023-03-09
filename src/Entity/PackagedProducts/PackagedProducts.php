<?php

declare(strict_types=1);

namespace App\Entity\PackagedProducts;

use App\Entity\Packaging\Packaging;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class PackagedProducts
{

    #[ORM\Id]
    #[ORM\Column(type: Types::STRING)]
    private string $productsHash;

    #[ORM\ManyToOne(targetEntity: Packaging::class)]
    #[ORM\JoinColumn(name: 'packaging_id', referencedColumnName: 'id')]
    private Packaging $availablePackaging;

    public function __construct(string $productsHash, Packaging $availablePackaging)
    {
        $this->productsHash = $productsHash;
        $this->availablePackaging = $availablePackaging;
    }

    public function getProductsHash(): string
    {
        return $this->productsHash;
    }

    public function getAvailablePackaging(): Packaging
    {
        return $this->availablePackaging;
    }


}
