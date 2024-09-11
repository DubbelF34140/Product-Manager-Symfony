<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user', name: 'app_user_')]
class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'list')]
    public function userList(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/account', name: 'account')]
    public function monCompte(): Response
    {     
        return $this->render('user/account.html.twig');
    }
}
