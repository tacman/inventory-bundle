<?php

namespace PlinioCardoso\InventoryBundle\Service;

use PlinioCardoso\InventoryBundle\Entity\Stock;
use PlinioCardoso\InventoryBundle\Message\StockUpdateNotification;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class StockImportManagement
{
    public function __construct(
        private StockService $stockService,
        private ProductService $productService,
        private WarehouseService $warehouseService,
        private MessageBusInterface $bus
    ) {}

    public function createOrUpdateStock(String $sku, int $quantity, String $location): void
    {
        $product = $this->productService->getProductBySku($sku);
        $warehouse = $this->warehouseService->getWarehouseByLocation($location);

        if ($product === null || $warehouse === null) {
            throw new NotFoundHttpException('Product or Warehouse not found - SKU: ' . $sku . ' Location: ' . $location);
        }

        $stock = $this->stockService->getStockByProductAndWarehouse($product, $warehouse);

        if ($stock === null) {
            $stock = Stock::of($product, $warehouse, $quantity);
        }

        $stock->setQuantity($quantity);
        $this->stockService->save($stock);
        $this->bus->dispatch(new StockUpdateNotification($stock));
    }
}
