<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MovementController extends AbstractController
{
    #[Route('/movement', name: 'app_movement')]
    public function index(): Response
    {
        return $this->render('movement/index.html.twig', [
            'controller_name' => 'MovementController',
        ]);
    }
}
