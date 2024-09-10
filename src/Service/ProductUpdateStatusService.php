<?php

namespace App\Service;

use App\Entity\Movement;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductUpdateStatusService
{
    private $productRepository;
    private $entityManager;

    public function __construct(ProductRepository $productRepository, EntityManagerInterface $entityManager)
    {
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Met à jour le statut de tous les produits dans un mouvement en fonction du type de mouvement.
     *
     * @param Movement $movement
     */
    public function updateProductStatus(Movement $movement): void
    {
        // Récupérer tous les produits liés au mouvement
        $products = $movement->getProducts();

        // Déterminer le nouveau statut en fonction du type de mouvement
        $newStatus = ($movement->getType());

        // Mettre à jour le statut de chaque produit
        foreach ($products as $product) {
            $product->setStatus($newStatus);
            $this->entityManager->persist($product);
        }

        // Sauvegarder les changements en base de données
        $this->entityManager->flush();
    }
}
