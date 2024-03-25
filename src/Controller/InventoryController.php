<?php

namespace PlinioCardoso\InventoryBundle\Controller;

use PlinioCardoso\InventoryBundle\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InventoryController extends AbstractController
{
    #[Route('/inventory/products/{id}/stocks', name: 'plinio_cardoso_inventory_stock')]
    public function displayStocksByProduct(int $id, ProductService $productService): Response
    {
        $product = $productService->getProduct($id);

        if ($product === null) {
            throw $this->createNotFoundException('Product not found');
        }

        return $this->render('@Inventory/inventory/index.html.twig', [
            'product' => $product
        ]);
    }
}
