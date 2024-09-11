<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProducFormType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        // Récupérer la valeur de la recherche
        $serialNumber = $request->query->get('serial_number');
        $product = null;

        // Si une recherche est effectuée
        if ($serialNumber) {
            // Chercher le produit par numéro de série
            $product = $productRepository->findOneBy(['serialNumber' => $serialNumber]);
        }else{
            $serialNumber = null;
        }

        return $this->render('scan/index.html.twig', [
            'product' => $product,
            'serial_number' => $serialNumber, // Transmettre le numéro de série pour créer un nouveau produit
        ]);
    }
}
