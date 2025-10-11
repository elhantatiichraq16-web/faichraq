<?php

namespace App\Controller;

use App\Repository\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard', name: 'dashboard')]
class DashboardController extends AbstractController
{
    public function __construct(
        private FactureRepository $factureRepository
    ) {
    }

    #[Route('', name: '')]
    public function index(): Response
    {
        $chiffreAffaires = $this->factureRepository->getChiffreAffairesMoisCourant();
        $nombreFacturesNonPayees = $this->factureRepository->countNonPayees();
        $dernieresFactures = $this->factureRepository->findDernieresFactures(5);

        return $this->render('dashboard/index.html.twig', [
            'chiffre_affaires' => $chiffreAffaires,
            'nombre_factures_non_payees' => $nombreFacturesNonPayees,
            'dernieres_factures' => $dernieresFactures,
        ]);
    }
}

