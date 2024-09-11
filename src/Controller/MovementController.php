<?php

namespace App\Controller;

use App\Entity\Movement;
use App\Form\MovementType;
use App\Repository\MovementRepository;
use App\Service\ProductUpdateStatusService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MovementController extends AbstractController
{

    private $productUpdateStatusService;

    public function __construct(ProductUpdateStatusService $productUpdateStatusService)
    {
        $this->productUpdateStatusService = $productUpdateStatusService;
    }


    #[IsGranted("ROLE_USER")]
    #[Route('/movement', name: 'app_movement_index')]
    public function index(MovementRepository $movementRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $search = $request->query->get('search', '');
        $query = $movementRepository->findBySearch($search);
        $page = $request->query->getInt('page', 1);
        $movements = $paginator->paginate($query, $page, 10);

        return $this->render('movement/index.html.twig', [
            'movements' => $movements,
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => ceil($movements->getTotalItemCount() / 10),
            'previousPage' => $page > 1 ? $page - 1 : null,
            'nextPage' => $page < ceil($movements->getTotalItemCount() / 10) ? $page + 1 : null,
        ]);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/movement/new', name: 'app_movement_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $movement = new Movement();
        $form = $this->createForm(MovementType::class, $movement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($movement);
            $em->flush();
            $this->productUpdateStatusService->updateProductStatus($movement);

            return $this->redirectToRoute('app_movement_index');
        }

        return $this->render('movement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/movement/{id}', name: 'app_movement_show')]
    public function show(Movement $movement): Response
    {
        return $this->render('movement/show.html.twig', [
            'movement' => $movement,
        ]);
    }
}
