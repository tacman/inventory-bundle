<?php

namespace PlinioCardoso\InventoryBundle\Message;

use PlinioCardoso\InventoryBundle\Entity\Stock;

readonly class StockUpdateNotification
{
    public function __construct(
        private Stock $stock,
    ) {}

    public function getContent(): Stock
    {
        return $this->stock;
    }
}
