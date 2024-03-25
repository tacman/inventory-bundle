<?php

namespace PlinioCardoso\InventoryBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

readonly class StockUpdateRequest
{
    public function __construct(
        #[Assert\NotBlank]
        public int $quantity,
    ) {}

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
