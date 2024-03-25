<?php

namespace PlinioCardoso\InventoryBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class InventoryBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
