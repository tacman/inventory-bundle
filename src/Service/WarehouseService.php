<?php

namespace PlinioCardoso\InventoryBundle\Service;

use PlinioCardoso\InventoryBundle\Entity\Warehouse;
use PlinioCardoso\InventoryBundle\Repository\WarehouseRepository;

class WarehouseService
{
    public function __construct(
        readonly private WarehouseRepository $repository
    ) {}

    public function getWarehouse(int $id): ?Warehouse
    {
        return $this->repository->find($id);
    }

    public function getWarehouseByCode(string $warehouseCode): ?Warehouse
    {
        return $this->repository->findOneBy(['code' => $warehouseCode]);
    }
}
