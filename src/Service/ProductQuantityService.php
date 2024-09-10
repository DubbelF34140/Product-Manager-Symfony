<?php


namespace App\Service;

use App\Entity\ProductType;
use App\Repository\ProductRepository;

class ProductQuantityService
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Récupère la quantité de produits par type de produit.
     *
     * @return int La quantité de produits.
     */
    public function getProductQuantitiesByType(ProductType $productType): int
    {
        $qb = $this->productRepository->createQueryBuilder('p');
        $qb->where('p.productType = :productType')
            ->andWhere('p.status != :status')
            ->setParameter('productType', $productType)
            ->setParameter('status', 'Poubelle');

        $products = $qb->getQuery()->getResult();
        return count($products);
    }

    /**
     * Récupère les produits par type.
     *
     * @return array Les produits du type spécifié.
     */
    public function getProductByType(ProductType $productType): array
    {
        return $this->productRepository->findBy(['productType' => $productType]);
    }
}