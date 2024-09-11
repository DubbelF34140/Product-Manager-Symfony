<?php

namespace App\Service;

use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Exception\GenerateException;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\HttpFoundation\Response;

class QrCodeService
{
    private BuilderInterface $builder;

    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Génère un QR code à partir d'un numéro de série
     *
     * @param string $serialNumber
     * @return Response
     * @throws GenerateException
     */
    public function generateQrCode(string $serialNumber): Response
    {
        // Générer le QR code avec le numéro de série
        $result = $this->builder
            ->data($serialNumber)
            ->writer(new PngWriter())
            ->size(300)
            ->margin(10)
            ->build();

        // Créer une réponse avec le QR code
        $response = new Response($result->getString(), Response::HTTP_OK, ['Content-Type' => $result->getMimeType()]);
        return $response;
    }
}
