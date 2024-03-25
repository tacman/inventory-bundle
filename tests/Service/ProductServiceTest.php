<?php

namespace PlinioCardoso\InventoryBundle\Tests\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PlinioCardoso\InventoryBundle\Entity\Product;
use PlinioCardoso\InventoryBundle\Repository\ProductRepository;
use PlinioCardoso\InventoryBundle\Service\ProductService;

class ProductServiceTest extends TestCase
{
    private MockObject $repository;
    private ProductService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ProductRepository::class);
        $this->service = new ProductService($this->repository);
    }

    public function testShouldFindProductById(): void
    {
        $this->repository->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn(new Product());

        $result = $this->service->getProduct(1);

        $this->assertInstanceOf(Product::class, $result);
    }

    public function testShouldLoadProductBySku(): void
    {
        $this->repository->expects(self::once())
            ->method('findOneBy')
            ->with(['sku' => 'abc'])
            ->willReturn(new Product());

        $result = $this->service->getProductBySku("abc");

        $this->assertInstanceOf(Product::class, $result);
    }
}
