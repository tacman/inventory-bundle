<?php

namespace PlinioCardoso\InventoryBundle\MessageHandler;

use PlinioCardoso\InventoryBundle\Message\StockUpdateNotification;

interface StockUpdateHandlerInterface
{
    public function __invoke(StockUpdateNotification $message): void;
}
