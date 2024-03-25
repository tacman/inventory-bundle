<?php

namespace PlinioCardoso\InventoryBundle\Service;

use PlinioCardoso\InventoryBundle\Entity\Warehouse;
use PlinioCardoso\InventoryBundle\Repository\WarehouseRepository;

class WarehouseService
{
    public function __construct(
        readonly private WarehouseRepository $repository
    ) {}

    public function getWarehouseByLocation(string $location): ?Warehouse
    {
        return $this->repository->findOneBy(['code' => $location]);
    }
}
