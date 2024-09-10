<?php

namespace App\Controller;

use App\Entity\Movement;
use App\Form\MovementType;
use App\Repository\MovementRepository;
use App\Service\ProductUpdateStatusService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovementController extends AbstractController
{

    private $productUpdateStatusService;

    public function __construct(ProductUpdateStatusService $productUpdateStatusService)
    {
        $this->productUpdateStatusService = $productUpdateStatusService;
    }



    #[Route('/movement', name: 'app_movement_index')]
    public function index(MovementRepository $movementRepository): Response
    {
        $movements = $movementRepository->findAll();
        return $this->render('movement/index.html.twig', [
            'movements' => $movements,
        ]);
    }
    #[Route('/movement/new', name: 'app_movement_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $movement = new Movement();
        $form = $this->createForm(MovementType::class, $movement);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder le mouvement
            $em->persist($movement);
            $em->flush();

            // Mettre à jour les statuts des produits associés au mouvement
            $this->productUpdateStatusService->updateProductStatus($movement);

            return $this->redirectToRoute('app_movement_index');
        }

        return $this->render('movement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/movement/{id}', name: 'app_movement_show')]
    public function show(Movement $movement): Response
    {
        return $this->render('movement/show.html.twig', [
            'movement' => $movement,
        ]);
    }
}
