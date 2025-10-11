<?php

namespace App\Service;

use App\Repository\FactureRepository;

class ReferenceGenerator
{
    private FactureRepository $factureRepository;

    public function __construct(FactureRepository $factureRepository)
    {
        $this->factureRepository = $factureRepository;
    }

    /**
     * Génère une référence unique pour une facture
     * Format: FAC-AAAA-0001
     */
    public function generateReference(): string
    {
        $annee = date('Y');
        $derniereReference = $this->factureRepository->findDerniereReference();

        if ($derniereReference && strpos($derniereReference, "FAC-{$annee}-") === 0) {
            // Extraire le numéro de la dernière référence
            $numero = (int) substr($derniereReference, -4);
            $nouveauNumero = $numero + 1;
        } else {
            // Première facture de l'année
            $nouveauNumero = 1;
        }

        return sprintf('FAC-%s-%04d', $annee, $nouveauNumero);
    }
}

