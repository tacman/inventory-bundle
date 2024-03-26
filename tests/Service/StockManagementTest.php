<?php

namespace tests\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PlinioCardoso\InventoryBundle\Entity\Product;
use PlinioCardoso\InventoryBundle\Entity\Stock;
use PlinioCardoso\InventoryBundle\Entity\Warehouse;
use PlinioCardoso\InventoryBundle\Model\StockDTO;
use PlinioCardoso\InventoryBundle\Service\ProductService;
use PlinioCardoso\InventoryBundle\Service\StockManagement;
use PlinioCardoso\InventoryBundle\Service\StockService;
use PlinioCardoso\InventoryBundle\Service\WarehouseService;
use stdClass;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class StockManagementTest extends TestCase
{
    private StockService|MockObject $stockService;
    private ProductService|MockObject $productService;
    private WarehouseService|MockObject $warehouseService;
    private MessageBusInterface|MockObject $bus;
    private StockManagement $service;

    protected function setUp(): void
    {
        $this->stockService = $this->createMock(StockService::class);
        $this->productService = $this->createMock(ProductService::class);
        $this->warehouseService = $this->createMock(WarehouseService::class);
        $this->bus = $this->createMock(MessageBusInterface::class);

        $this->service = new StockManagement(
            $this->stockService,
            $this->productService,
            $this->warehouseService,
            $this->bus
        );
    }

    public function testShouldCreateStock(): void
    {
        $this->productService->expects(self::once())
            ->method('getProduct')
            ->with(1)
            ->willReturn(new Product());

        $this->warehouseService->expects(self::once())
            ->method('getWarehouse')
            ->with(1)
            ->willReturn(new Warehouse());

        $this->stockService->expects(self::once())
            ->method('save');

        $stockDTO = new StockDTO(1, 10, 1, null);
        $this->service->handleStockCreateUpdate($stockDTO);
    }

    public function testShouldThrowExceptionForNonExistingProduct(): void
    {
        $stockDTO = new StockDTO(1, 10, 1, null);
        $this->expectException(NotFoundHttpException::class);

        $this->productService->expects(self::once())
            ->method('getProduct')
            ->with(1)
            ->willReturn(null);

        $this->service->handleStockCreateUpdate($stockDTO);
    }

    public function testShouldThrowExceptionForNonExistingWarehouse(): void
    {
        $stockDTO = new StockDTO(1, 10, 1, null);
        $this->expectException(NotFoundHttpException::class);

        $this->productService->expects(self::once())
            ->method('getProduct')
            ->with(1)
            ->willReturn(new Product());

        $this->warehouseService->expects(self::once())
            ->method('getWarehouse')
            ->with(1)
            ->willReturn(null);

        $this->service->handleStockCreateUpdate($stockDTO);
    }

    public function testShouldThrowExceptionWhenStockAlreadyExistsForProductAndWarehouse(): void
    {
        $stockDTO = new StockDTO(1, 10, 1, null);
        $this->expectException(NotFoundHttpException::class);

        $this->productService->expects(self::once())
            ->method('getProduct')
            ->with(1)
            ->willReturn(new Product());

        $this->warehouseService->expects(self::once())
            ->method('getWarehouse')
            ->with(1)
            ->willReturn(new Warehouse());

        $this->stockService->expects(self::once())
            ->method('exists')
            ->willReturn(true);

        $this->service->handleStockCreateUpdate($stockDTO);
    }

    public function testShouldUpdateStock(): void
    {
        $stock = $this->createMock(Stock::class);
        $this->stockService->expects(self::once())
            ->method('getStock')
            ->with(1)
            ->willReturn($stock);

        $stock->expects(self::once())
            ->method('setQuantity')
            ->with(10);

        $this->stockService->expects(self::once())
            ->method('save')
            ->with($stock);

        $this->bus->expects(self::once())
            ->method('dispatch')
            ->willReturn(new Envelope(new stdClass(), []));

        $this->service->handleStockCreateUpdate(
            new StockDTO(null, 10, null, 1)
        );
    }

    public function testShouldImportStock(): void
    {
        $this->productService->expects(self::once())
            ->method('getProductBySku')
            ->with('abc')
            ->willReturn(new Product());

        $this->warehouseService->expects(self::once())
            ->method('getWarehouseByCode')
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

        $this->service->importStock('abc', 1, 'dublin');
    }

    public function testShouldUpdateImportedStock(): void
    {
        $this->productService->expects(self::once())
            ->method('getProductBySku')
            ->with('abc')
            ->willReturn(new Product());

        $this->warehouseService->expects(self::once())
            ->method('getWarehouseByCode')
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

        $this->service->importStock('abc', 1, 'dublin');
    }

    public function testShouldThrowExceptionForNonExistingProductWhenImportingStock(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->productService->expects(self::once())
            ->method('getProductBySku')
            ->with('abc')
            ->willReturn(null);

        $this->service->importStock('abc', 1, 'dublin');
    }

    public function testShouldThrowExceptionForNonExistingWarehouseWhenImportingStock(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->productService->expects(self::once())
            ->method('getProductBySku')
            ->with('abc')
            ->willReturn(new Product());

        $this->warehouseService->expects(self::once())
            ->method('getWarehouseByCode')
            ->with('dublin')
            ->willReturn(null);

        $this->service->importStock('abc', 1, 'dublin');
    }
}
