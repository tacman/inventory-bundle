<?php

namespace PlinioCardoso\InventoryBundle\Service;

use PlinioCardoso\InventoryBundle\Entity\Stock;
use PlinioCardoso\InventoryBundle\Message\StockUpdateNotification;
use PlinioCardoso\InventoryBundle\Model\StockDTO;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class StockManagement
{
    public function __construct(
        private StockService $stockService,
        private ProductService $productService,
        private WarehouseService $warehouseService,
        private MessageBusInterface $bus
    ) {}

    public function handleStockCreateUpdate(StockDTO $stockDTO): Stock
    {
        if ($stockDTO->getStockId() === null) {
            return $this->createStock($stockDTO);
        }

        return $this->updateStock($stockDTO);
    }

    public function createStock(StockDTO $stockDTO): Stock
    {
        $product = $this->productService->getProduct($stockDTO->getProductId());
        $warehouse = $this->warehouseService->getWarehouse($stockDTO->getWarehouseId());

        if ($product === null) {
            throw new NotFoundHttpException('Product not found');
        }

        if ($warehouse === null) {
            throw new NotFoundHttpException('Warehouse not found');
        }

        if ($this->stockService->exists($product, $warehouse)) {
            throw new NotFoundHttpException('Stock already exists for this product and warehouse');
        }

        return $this->stockService->save(
            Stock::of($product, $warehouse, $stockDTO->getQuantity())
        );
    }

    public function updateStock(StockDTO $stockDTO): Stock
    {
        $stock = $this->stockService->getStock($stockDTO->getStockId());

        if ($stock === null) {
            throw new NotFoundHttpException('Stock not found');
        }

        $stock->setQuantity($stockDTO->getQuantity());
        $this->stockService->save($stock);
        $this->bus->dispatch(new StockUpdateNotification($stock));

        return $stock;
    }

    public function importStock(string $sku, int $quantity, string $warehouseCode): void
    {
        $product = $this->productService->getProductBySku($sku);
        $warehouse = $this->warehouseService->getWarehouseByCode($warehouseCode);

        if ($product === null || $warehouse === null) {
            throw new NotFoundHttpException(
                'Product or Warehouse not found - SKU: ' . $sku . ' Warehouse: ' . $warehouseCode
            );
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
