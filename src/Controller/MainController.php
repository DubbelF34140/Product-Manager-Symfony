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
        $productTypes = $productTypeRepository->findAll();
        $productTypeNames = [];
        $productCounts = [];
        foreach ($productTypes as $productType) {
            $productTypeNames[] = $productType->getName();
            $productCounts[] = $productQuantityService->getProductQuantitiesByType($productType);
        }

        return $this->render('home/home.html.twig', [
            'productTypeNames' => json_encode($productTypeNames),
            'productCounts' => json_encode($productCounts),
        ]);
    }
}
