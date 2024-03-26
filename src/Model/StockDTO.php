<?php

namespace PlinioCardoso\InventoryBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class StockDTO
{
    public function __construct(
        public ?int $productId,

        #[Assert\NotBlank]
        public int $quantity,

        public ?string $warehouseId,

        public ?int $stockId,
    ) {}

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getStockId(): ?int
    {
        return $this->stockId;
    }

    public function setStockId(int $stockId): void
    {
        $this->stockId = $stockId;
    }

    public function getWarehouseId(): ?int
    {
        return $this->warehouseId;
    }
}
