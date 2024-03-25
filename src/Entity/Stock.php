<?php

namespace PlinioCardoso\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PlinioCardoso\InventoryBundle\Repository\StockRepository;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Warehouse $location = null;

    #[ORM\ManyToOne(inversedBy: 'stocks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getLocation(): ?Warehouse
    {
        return $this->location;
    }

    public function setLocation(?Warehouse $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public static function of(Product $product, Warehouse $warehouse, int $quantity): Stock
    {
        $stock = new Stock();
        $stock->setProduct($product);
        $stock->setLocation($warehouse);
        $stock->setQuantity($quantity);

        return $stock;
    }
}
