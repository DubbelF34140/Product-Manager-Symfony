<?php

namespace App\Controller;

use App\Entity\Movement;
use App\Entity\Product;
use App\Form\ProducFormType;
use App\Repository\ProductRepository;
use App\Repository\ProductTypeRepository;
use App\Service\ProductQuantityService;
use App\Service\QrCodeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProductController extends AbstractController
{
    #[IsGranted("ROLE_USER")]
    #[Route('/product', name: 'app_product_index')]
    public function index(
        ProductRepository $productRepository,
        ProductTypeRepository $productTypeRepository,
        Request $request,
        EntityManagerInterface $em,
        ProductQuantityService $productQuantityService,
        PaginatorInterface $paginator // Pour la pagination
    ): Response {
        $search = $request->query->get('search', '');

        $productTypesQuery = $productTypeRepository->findBySearchTerm($search);

        $page = $request->query->getInt('page', 1);

        $paginatedProductTypes = $paginator->paginate($productTypesQuery, $page, 8);

        $productsByType = [];

        foreach ($paginatedProductTypes as $type) {
            $productsByType[] = [
                'id' => $type->getId(),
                'name' => $type->getName(),
                'quantity' => $productQuantityService->getProductQuantitiesByType($type),
                'serialNumbers' => $productQuantityService->getProductByType($type)
            ];
        }

        $product = new Product();
        $form = $this->createForm(ProducFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $movement = new Movement();
            $movement->addProduct($product);
            $movement->setQuantity(1);
            $movement->setDate(new \DateTime());
            $movement->setType("Entrée");
            $product->setStatus("Entrée");

            $em->persist($movement);
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/index.html.twig', [
            'productsByType' => $productsByType,
            'form' => $form->createView(),
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => ceil($paginatedProductTypes->getTotalItemCount() / 8),
            'previousPage' => $page > 1 ? $page - 1 : null,
            'nextPage' => $page < ceil($paginatedProductTypes->getTotalItemCount() / 8) ? $page + 1 : null,
        ]);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/product/{id}/sav', name: 'app_product_sav', methods: ['POST'])]
    public function savProduct(int $id,ProductRepository $productRepository, EntityManagerInterface $em): Response
    {
        // Mettre à jour le statut du produit (par exemple, "SAV" ou "E/S")
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('product not found');
        }
        dump($product);
        $movement = new Movement();
        $movement->addProduct($product);
        $movement->setQuantity(1);
        $movement->setDate(new \DateTime());
        $movement->setType("SAV");
        $product->setStatus("SAV");

        $em->persist($movement);
        $em->persist($product);
        $em->flush();

        $this->addFlash('success', 'Le produit à bien été envoyé en SAV.');
        // Rediriger vers la liste des produits après modification
        return $this->redirectToRoute('app_product_index');
    }
    #[IsGranted("ROLE_USER")]
    #[Route('/product/{id}/rep', name: 'app_product_rep', methods: ['POST'])]
    public function repProduct(int $id,ProductRepository $productRepository, EntityManagerInterface $em): Response
    {
        // Mettre à jour le statut du produit (par exemple, "SAV" ou "E/S")
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('product not found');
        }
        dump($product);
        $movement = new Movement();
        $movement->addProduct($product);
        $movement->setQuantity(1);
        $movement->setDate(new \DateTime());
        $movement->setType("Réparation");
        $product->setStatus("Réparation");

        $em->persist($movement);
        $em->persist($product);
        $em->flush();
        $this->addFlash('success', 'Le produit à bien été envoyé en réparation.');


        // Rediriger vers la liste des produits après modification
        return $this->redirectToRoute('app_product_index');
    }
    #[IsGranted("ROLE_USER")]
    #[Route('/product/{id}', name: 'app_product_show', requirements: ['id' => '\d+'])]
    public function show(int $id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
    #[IsGranted("ROLE_USER")]
    #[Route('/product/{id}/delete', name: 'app_product_delete')]
    public function delete(EntityManagerInterface $em, Product $product): Response
    {
        // Supprimer le produit
        $movement = new Movement();
        $movement->addProduct($product);
        $movement->setQuantity(1);
        $movement->setDate(new \DateTime());
        $movement->setType("Poubelle");
        $product->setStatus("Poubelle");
        $em->persist($movement);
        $em->persist($product);
        $em->flush();

        $this->addFlash('success', 'Le produit a été surpprimé avec succès.');

        return $this->redirectToRoute('app_product_index');
    }
    #[IsGranted("ROLE_USER")]
    #[Route('/product/{id}/serials', name: 'app_product_serials')]
    public function showSerialNumbers(Product $product): Response
    {
        return $this->render('product/serial_numbers.html.twig', [
            'product' => $product,
            'serialNumbers' => $product->getSerialNumber(),
        ]);
    }
    #[IsGranted("ROLE_USER")]
    #[Route('/product/{id}/remove_serial', name: 'app_product_remove_serial', methods: ['POST'])]
    public function removeSerialNumber(Request $request, Product $product, EntityManagerInterface $em): Response
    {
        $serialNumber = $request->request->get('serial_number');
        $product->removeSerialNumber($serialNumber);

        $em->flush();
        $this->addFlash('success', 'Le produit a été surpprimé avec succès.');


        return $this->redirectToRoute('app_product_index');
    }
    #[IsGranted("ROLE_USER")]
    #[Route('/product/export', name: 'app_product_export')]
    public function export(): Response
    {
        // Logique d'export en PDF
        return new Response("Export des produits en PDF");
    }


    #[IsGranted("ROLE_USER")]
    #[Route('/product/new', name: 'app_product_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        // Récupérer le numéro de série depuis l'URL s'il est présent
        $serialNumber = $request->query->get('serialNumber');

        // Créer un nouvel objet Product
        $product = new Product();

        // Si un numéro de série est passé, on le pré-remplit dans le produit
        if ($serialNumber) {
            $product->setSerialNumber($serialNumber);
        }

        // Créer le formulaire pour le produit
        $form = $this->createForm(ProducFormType::class, $product);

        // Gérer la requête du formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Créer un mouvement d'entrée pour ce produit
            $movement = new Movement();
            $movement->addProduct($product);
            $movement->setQuantity(1);
            $movement->setDate(new \DateTime());
            $movement->setType('Entrée');
            $product->setStatus('Entrée');

            // Sauvegarder le produit et le mouvement dans la base de données
            $em->persist($movement);
            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Le produit a été créer avec succès.');

            // Rediriger vers la page des produits après la création
            return $this->redirectToRoute('app_product_index');
        }

        // Afficher la page d'ajout du produit
        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[IsGranted("ROLE_USER")]
    #[Route('/product/{id}/edit', name: 'app_product_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Product $product, ProductTypeRepository $productTypeRepository,ProductRepository $productRepository, EntityManagerInterface $em): Response
    {

        $product = $productRepository->find($product->getId());

        $productTypes = $productTypeRepository->findAll();

        // Traitement du formulaire
        if ($request->isMethod('POST')) {
            // Récupérer les données du formulaire
            $productTypeId = $request->request->get('product_type');
            $status = $request->request->get('status');
            $comment = $request->request->get('comment');

            // Mettre à jour les informations du produit
            $productType = $productTypeRepository->find($productTypeId);
            $product->setProductType($productType);
            $product->setStatus($status);
            $product->setComment($comment);

            // Créer un mouvement pour cette modification
            $movement = new Movement();
            $movement->addProduct($product);
            $movement->setQuantity(1);
            $movement->setDate(new \DateTime());
            $movement->setType($status);
            $em->persist($movement);

            // Sauvegarder les modifications
            $em->flush();
            $this->addFlash('success', 'Le produit a été modifié avec succès.');


            return $this->redirectToRoute('app_product_index');
        }

        // Passer les types de produits et le produit au template
        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'productTypes' => $productTypes,
        ]);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/product/{id}/qrcode', name: 'app_product_qrcode')]
    public function generateQrCode(Product $product, QrCodeService $qrCodeService): Response
    {
        return $qrCodeService->generateQrCode($product->getSerialNumber());
    }

}