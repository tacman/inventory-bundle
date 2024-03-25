<?php

namespace tests\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PlinioCardoso\InventoryBundle\Entity\Product;
use PlinioCardoso\InventoryBundle\Entity\Stock;
use PlinioCardoso\InventoryBundle\Entity\Warehouse;
use PlinioCardoso\InventoryBundle\Service\ProductService;
use PlinioCardoso\InventoryBundle\Service\StockImportManagement;
use PlinioCardoso\InventoryBundle\Service\StockService;
use PlinioCardoso\InventoryBundle\Service\WarehouseService;
use stdClass;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class StockImportManagementTest extends TestCase
{
    private StockService|MockObject $stockService;
    private ProductService|MockObject $productService;
    private WarehouseService|MockObject $warehouseService;
    private MessageBusInterface|MockObject $bus;
    private StockImportManagement $service;

    protected function setUp(): void
    {
        $this->stockService = $this->createMock(StockService::class);
        $this->productService = $this->createMock(ProductService::class);
        $this->warehouseService = $this->createMock(WarehouseService::class);
        $this->bus = $this->createMock(MessageBusInterface::class);

        $this->service = new StockImportManagement(
            $this->stockService,
            $this->productService,
            $this->warehouseService,
            $this->bus
        );
    }

    public function testShouldCreateStock(): void
    {
        $this->productService->expects(self::once())
            ->method('getProductBySku')
            ->with('abc')
            ->willReturn(new Product());

        $this->warehouseService->expects(self::once())
            ->method('getWarehouseByLocation')
            ->with('dublin')
            ->willReturn(new Warehouse());

        $this->stockService->expects(self::once())
            ->method('getStockByProductAndWarehouse')
            ->willReturn(null);

        $this->stockService->expects(self::once())
            ->method('save');

        $this->bus->expects(self::once())
            ->method('dispatch')
            ->willReturn(new Envelope(new stdClass(), []));

        $this->service->createOrUpdateStock('abc', 1, 'dublin');
    }

    public function testShouldUpdateStock(): void
    {
        $this->productService->expects(self::once())
            ->method('getProductBySku')
            ->with('abc')
            ->willReturn(new Product());

        $this->warehouseService->expects(self::once())
            ->method('getWarehouseByLocation')
            ->with('dublin')
            ->willReturn(new Warehouse());

        $stock = $this->createMock(Stock::class);
        $this->stockService->expects(self::once())
            ->method('getStockByProductAndWarehouse')
            ->willReturn($stock);

        $stock->expects(self::once())
            ->method('setQuantity')
            ->with(1);

        $this->stockService->expects(self::once())
            ->method('save');

        $this->bus->expects(self::once())
            ->method('dispatch')
            ->willReturn(new Envelope(new stdClass(), []));

        $this->service->createOrUpdateStock('abc', 1, 'dublin');
    }

    public function testShouldThrowExceptionForNonExistingProduct(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->productService->expects(self::once())
            ->method('getProductBySku')
            ->with('abc')
            ->willReturn(null);

        $this->service->createOrUpdateStock('abc', 1, 'dublin');
    }

    public function testShouldThrowExceptionForNonExistingWarehouse(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->productService->expects(self::once())
            ->method('getProductBySku')
            ->with('abc')
            ->willReturn(new Product());

        $this->warehouseService->expects(self::once())
            ->method('getWarehouseByLocation')
            ->with('dublin')
            ->willReturn(null);

        $this->service->createOrUpdateStock('abc', 1, 'dublin');
    }
}
