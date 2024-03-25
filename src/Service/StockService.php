<?php

namespace PlinioCardoso\InventoryBundle\Service;

use PlinioCardoso\InventoryBundle\Entity\Product;
use PlinioCardoso\InventoryBundle\Entity\Stock;
use PlinioCardoso\InventoryBundle\Entity\Warehouse;
use PlinioCardoso\InventoryBundle\Repository\StockRepository;

class StockService
{
    public function __construct(
        readonly private StockRepository $repository
    ) {}

    public function getStock(int $id): ?Stock
    {
        return $this->repository->find($id);
    }

    public function getStockByProductAndWarehouse(Product $product, Warehouse $warehouse): ?Stock
    {
        return $this->repository->findOneBy(['product' => $product, 'location' => $warehouse]);
    }

    public function exists(Product $product, Warehouse $warehouse): bool
    {
        return $this->repository->exists(
            ['product' => $product,'location' => $warehouse]
        );
    }

    public function save(Stock $stock): Stock
    {
        return $this->repository->save($stock);
    }
}
