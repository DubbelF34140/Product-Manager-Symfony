<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Form\BrandType;
use App\Repository\BrandRepository;
use App\Repository\ProductTypeRepository;
use App\Service\ProductQuantityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BrandController extends AbstractController
{
    #[Route('/brand', name: 'app_brand_index')]
    public function index(BrandRepository $brandRepository): Response
    {
        $brands = $brandRepository->findAll();
        return $this->render('brand/index.html.twig', [
            'brands' => $brands,
        ]);
    }

    #[Route('/brand/new', name: 'app_brand_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($brand);
            $em->flush();
            return $this->redirectToRoute('app_brand_index');
        }

        return $this->render('brand/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/brand/{id}', name: 'app_brand_show')]
    public function show(int $id, BrandRepository $brandRepository, ProductTypeRepository $productTypeRepository, ProductQuantityService $productQuantityService): Response
    {
        // Récupérer la marque
        $brand = $brandRepository->find($id);

        // Récupérer tous les types de produits associés à la marque
        $productTypes = $productTypeRepository->findBy(['brand' => $brand]);

        // Calculer le nombre de produits par type de produit
        $productCounts = [];
        foreach ($productTypes as $productType) {
            $productCounts[] = $productQuantityService->getProductQuantitiesByType($productType);  // Assurez-vous que l'entité ProductType a une relation avec les produits.
        }


        return $this->render('brand/show.html.twig', [
            'brand' => $brand,
            'productTypes' => $productTypes,
            'productCounts' => $productCounts,
        ]);
    }

    #[Route('/brand/{id}/edit', name: 'app_brand_edit')]
    public function edit(Request $request, Brand $brand, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BrandType::class, $brand);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_brand_index');
        }

        return $this->render('brand/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/brand/{id}/delete', name: 'app_brand_delete')]
    public function delete(EntityManagerInterface $em, Brand $brand): Response
    {
        $em->remove($brand);
        $em->flush();
        return $this->redirectToRoute('app_brand_index');
    }
}
