<?php

namespace PlinioCardoso\InventoryBundle\Service;

use PlinioCardoso\InventoryBundle\Entity\Product;
use PlinioCardoso\InventoryBundle\Repository\ProductRepository;

class ProductService
{
    public function __construct(
        readonly private ProductRepository $repository
    ) {}

    public function getProduct(int $id): ?Product
    {
        return $this->repository->find($id);
    }

    public function getProductBySku(string $sku): ?Product
    {
        return $this->repository->findOneBy(['sku' => $sku]);
    }
}
