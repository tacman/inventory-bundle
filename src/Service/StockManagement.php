<?php

namespace PlinioCardoso\InventoryBundle\Service;

use PlinioCardoso\InventoryBundle\Entity\Stock;
use PlinioCardoso\InventoryBundle\Message\StockUpdateNotification;
use PlinioCardoso\InventoryBundle\Model\StockDTO;
use PlinioCardoso\InventoryBundle\Model\StockUpdateRequest;
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

    public function createStock(StockDTO $stockDTO): Stock
    {
        $product = $this->productService->getProduct($stockDTO->getProductId());
        $warehouse = $this->warehouseService->getWarehouseByLocation($stockDTO->getLocation());

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

    public function updateStock(int $stockId, StockUpdateRequest $stockUpdateRequest): void
    {
        $stock = $this->stockService->getStock($stockId);

        if ($stock === null) {
            throw new NotFoundHttpException('Stock not found');
        }

        $stock->setQuantity($stockUpdateRequest->getQuantity());
        $this->stockService->save($stock);
        $this->bus->dispatch(new StockUpdateNotification($stock));
    }
}
