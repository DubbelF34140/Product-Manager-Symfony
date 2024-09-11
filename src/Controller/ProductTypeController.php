<?php

namespace App\Controller;

use App\Entity\ProductType;
use App\Form\ProducTypeFormType;
use App\Repository\CategoryRepository;
use App\Repository\ProductTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProductTypeController extends AbstractController
{
    #[IsGranted("ROLE_USER")]
    #[Route('/producttype', name: 'app_product_type_index')]
    public function index(ProductTypeRepository $productTypeRepository, CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginator): Response {
        $search = $request->query->get('search', '');
        $category = $request->query->get('category', null);
        $page = $request->query->getInt('page', 1);
        $productTypesQuery = $productTypeRepository->findBySearchTermAndCategory($search, $category);
        $paginatedProductTypes = $paginator->paginate($productTypesQuery, $page, 8);
        $categorys = $categoryRepository->findAll();
        dump($categorys);

        return $this->render('product_type/index.html.twig', [
            'product_types' => $paginatedProductTypes,
            'search' => $search,
            'categorys' => $categorys,
            'category' => $category,
            'currentPage' => $page,
            'totalPages' => ceil($paginatedProductTypes->getTotalItemCount() / 8),
            'previousPage' => $page > 1 ? $page - 1 : null,
            'nextPage' => $page < ceil($paginatedProductTypes->getTotalItemCount() / 8) ? $page + 1 : null,
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/producttype/new', name: 'app_product_type_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $productType = new ProductType();
        $form = $this->createForm(ProducTypeFormType::class, $productType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($productType);
            $em->flush();
            $this->addFlash('success', 'Le type de produit a été créé avec succès.');

            return $this->redirectToRoute('app_product_type_index');
        }
        return $this->render('product_type/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/producttype/{id}', name: 'app_product_type_show')]
    public function show(ProductType $productType): Response
    {
        return $this->render('product_type/show.html.twig', [
            'product_type' => $productType,
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/producttype/{id}/edit', name: 'app_product_type_edit')]
    public function edit(Request $request, ProductType $productType, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProducTypeFormType::class, $productType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Le type de produit a été modifié avec succès.');

            return $this->redirectToRoute('app_product_type_index');
        }

        return $this->render('product_type/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/producttype/{id}/delete', name: 'app_product_type_delete')]
    public function delete(EntityManagerInterface $em, ProductType $productType): Response
    {
        $em->remove($productType);
        $em->flush();
        $this->addFlash('success', 'Le type de produit a été supprimé avec succès.');

        return $this->redirectToRoute('app_product_type_index');
    }
}
