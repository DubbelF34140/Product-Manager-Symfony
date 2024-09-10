<?php

namespace App\Controller;

use App\Entity\ProductType;
use App\Form\ProducTypeFormType;
use App\Repository\ProductTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductTypeController extends AbstractController
{
    #[Route('/producttype', name: 'app_product_type_index')]
    public function index(ProductTypeRepository $productTypeRepository): Response
    {
        $productTypes = $productTypeRepository->findAll();
        return $this->render('product_type/index.html.twig', [
            'product_types' => $productTypes,
        ]);
    }

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

    #[Route('/producttype/{id}', name: 'app_product_type_show')]
    public function show(ProductType $productType): Response
    {
        return $this->render('product_type/show.html.twig', [
            'product_type' => $productType,
        ]);
    }

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

    #[Route('/producttype/{id}/delete', name: 'app_product_type_delete')]
    public function delete(EntityManagerInterface $em, ProductType $productType): Response
    {
        $em->remove($productType);
        $em->flush();
        $this->addFlash('success', 'Le type de produit a été supprimé avec succès.');
        return $this->redirectToRoute('app_product_type_index');
    }
}
