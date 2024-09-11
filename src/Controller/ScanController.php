<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ScanController extends AbstractController
{
    #[Route('/scan', name: 'app_scan')]
    #[IsGranted("ROLE_USER")]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $serialNumber = $request->query->get('serial_number');
        $product = null;
        if ($serialNumber) {
            $product = $productRepository->findOneBy(['serialNumber' => $serialNumber]);
        }else{
            $serialNumber = null;
        }

        return $this->render('scan/index.html.twig', [
            'product' => $product,
            'serial_number' => $serialNumber,
        ]);
    }
}
