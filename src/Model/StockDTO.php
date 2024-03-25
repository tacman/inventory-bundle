<?php

namespace PlinioCardoso\InventoryBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

readonly class StockDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public int $productId,

        #[Assert\NotBlank]
        public int $quantity,

        #[Assert\NotBlank]
        public string $location
    ) {}

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getLocation(): string
    {
        return $this->location;
    }
}
