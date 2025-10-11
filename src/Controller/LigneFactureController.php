<?php

namespace App\Controller;

use App\Entity\LigneFacture;
use App\Repository\LigneFactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ligne-facture')]
class LigneFactureController extends AbstractController
{
    public function __construct(
        private LigneFactureRepository $ligneFactureRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/{id}', name: 'ligne_facture_delete', methods: ['DELETE'])]
    public function delete(Request $request, LigneFacture $ligneFacture): JsonResponse
    {
        if ($this->isCsrfTokenValid('delete' . $ligneFacture->getId(), $request->request->get('_token'))) {
            $facture = $ligneFacture->getFacture();
            $this->entityManager->remove($ligneFacture);
            $facture->calculerTotaux();
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'total_ht' => $facture->getTotalHt(),
                'total_ttc' => $facture->getTotalTtc(),
                'total_tva' => $facture->getTotalTva(),
            ]);
        }

        return new JsonResponse(['success' => false], 400);
    }
}

