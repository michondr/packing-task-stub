<?php

declare(strict_types=1);

namespace App\BinPacking;

use App\Entity\Packaging\Packaging;

readonly class PackagingTransformer
{

    /**
     *
     * @param Packaging[] $packagings
     *
     * @return array
     */
    public function transformPackagingToBins(array $packagings): array
    {
        return array_map(
            fn(Packaging $p) => $this->transformSinglePackagingToBin($p),
            $packagings,
        );
    }

    /**
     * @param Packaging $packaging
     *
     * @return array<string, int|float>
     */
    private function transformSinglePackagingToBin(Packaging $packaging): array
    {
        return [
            'id' => $packaging->getId(),
            'h' => $packaging->getHeight(),
            'w' => $packaging->getWidth(),
            'd' => $packaging->getLength(),
            'wg' => 0,
            'max_wg' => $packaging->getMaxWeight(),
            'q' => null,
            'cost' => 0,
        ];
    }
}
