<?php

namespace PlinioCardoso\InventoryBundle\Controller;

use PlinioCardoso\InventoryBundle\Model\StockDTO;
use PlinioCardoso\InventoryBundle\Model\StockUpdateRequest;
use PlinioCardoso\InventoryBundle\Service\StockManagement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/inventory', name: 'plinio_cardoso_inventory_api_', format: 'json')]
class InventoryApiController extends AbstractController
{
    #[Route('/stocks', name: 'stock_create', methods:['post'])]
    public function createStock(
        #[MapRequestPayload(acceptFormat: 'json')] StockDTO $stockDTO,
        StockManagement $stockManagement
    ): Response
    {
        $stock = $stockManagement->createStock($stockDTO);

        return $this->json(
            ['id' => $stock->getId()], Response::HTTP_CREATED
        );
    }

    #[Route('/stocks/{id}', name: 'stock_update', methods:['patch'])]
    public function updateStock(
        int $id,
        #[MapRequestPayload(acceptFormat: 'json')] StockUpdateRequest $stockUpdateRequest,
        StockManagement $stockManagement
    ): Response
    {
        $stockManagement->updateStock($id, $stockUpdateRequest);
        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
