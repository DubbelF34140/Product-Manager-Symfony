<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Form\BrandType;
use App\Repository\BrandRepository;
use App\Repository\ProductTypeRepository;
use App\Service\ProductQuantityService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BrandController extends AbstractController
{

    #[IsGranted("ROLE_USER")]
    #[Route('/brand', name: 'app_brand_index')]
    public function index(BrandRepository $brandRepository, Request $request, PaginatorInterface $paginator): Response {
        $search = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);
        $brandsQuery = $brandRepository->findBySearchTerm($search);
        $brands = $paginator->paginate($brandsQuery, $page, 8);

        return $this->render('brand/index.html.twig', [
            'brands' => $brands,
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => ceil($brands->getTotalItemCount() / 8),
            'previousPage' => $page > 1 ? $page - 1 : null,
            'nextPage' => $page < ceil($brands->getTotalItemCount() / 8) ? $page + 1 : null,
        ]);
    }
    #[IsGranted("ROLE_ADMIN")]
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

    #[IsGranted("ROLE_USER")]
    #[Route('/brand/{id}', name: 'app_brand_show')]
    public function show(int $id, BrandRepository $brandRepository, ProductTypeRepository $productTypeRepository, ProductQuantityService $productQuantityService): Response
    {
        $brand = $brandRepository->find($id);
        $productTypes = $productTypeRepository->findBy(['brand' => $brand]);
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

    #[IsGranted("ROLE_ADMIN")]
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

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/brand/{id}/delete', name: 'app_brand_delete')]
    public function delete(EntityManagerInterface $em, Brand $brand): Response
    {
        $em->remove($brand);
        $em->flush();

        return $this->redirectToRoute('app_brand_index');
    }
}
