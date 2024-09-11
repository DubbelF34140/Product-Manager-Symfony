<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProductTypeRepository;
use App\Service\ProductQuantityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main_index')]
    public function index(ProductTypeRepository $productTypeRepository, ProductQuantityService $productQuantityService): Response
    {
        // Récupérer tous les types de produits
        $productTypes = $productTypeRepository->findAll();

        // Préparer les données pour le graphique
        $productTypeNames = [];
        $productCounts = [];

        foreach ($productTypes as $productType) {
            $productTypeNames[] = $productType->getName();  // Nom du type de produit
            $productCounts[] = $productQuantityService->getProductQuantitiesByType($productType);  // Quantité en stock par type
        }

        // Passer les données au template
        return $this->render('home/home.html.twig', [
            'productTypeNames' => json_encode($productTypeNames),  // Convertir en JSON pour Chart.js
            'productCounts' => json_encode($productCounts),  // Convertir en JSON pour Chart.js
        ]);
    }
}
