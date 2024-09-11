<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\ProductTypeRepository;
use App\Service\ProductQuantityService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CategoryController extends AbstractController
{

    #[IsGranted("ROLE_USER")]
    #[Route('/category', name: 'app_category_index')]
    public function index(CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginator ): Response {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);
        $categoriesQuery = $categoryRepository->findBySearchTerm($search);
        $paginatedCategories = $paginator->paginate($categoriesQuery, $page, 8);

        return $this->render('category/index.html.twig', [
            'categories' => $paginatedCategories,
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => ceil($paginatedCategories->getTotalItemCount() / 8),
            'previousPage' => $page > 1 ? $page - 1 : null,
            'nextPage' => $page < ceil($paginatedCategories->getTotalItemCount() / 8) ? $page + 1 : null,
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/category/new', name: 'app_category_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('app_category_index');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[IsGranted("ROLE_USER")]
    #[Route('/category/{id}', name: 'app_category_show')]
    public function show(int $id, CategoryRepository $categoryRepository, ProductTypeRepository $productTypeRepository, ProductQuantityService $productQuantityService): Response
    {
        $category = $categoryRepository->find($id);
        $productTypes = $productTypeRepository->findBy(['category' => $category]);
        $productCounts = [];
        foreach ($productTypes as $productType) {
            $productCounts[] = $productQuantityService->getProductQuantitiesByType($productType);
        }

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'productTypes' => $productTypes,
            'productCounts' => $productCounts,
        ]);
    }


    #[IsGranted("ROLE_ADMIN")]
    #[Route('/category/{id}/edit', name: 'app_category_edit')]
    public function edit(Request $request, Category $category, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_category_index');
        }

        return $this->render('category/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/category/{id}/delete', name: 'app_category_delete')]
    public function delete(EntityManagerInterface $em, Category $category): Response
    {
        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('app_category_index');
    }
}
