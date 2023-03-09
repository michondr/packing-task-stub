<?php

declare(strict_types=1);

namespace App\Product;

readonly class Product
{
    public function __construct(
        private int $id,
        private float $width,
        private float $height,
        private float $length,
        private float $weight,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

}
