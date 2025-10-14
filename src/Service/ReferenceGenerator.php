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
            $numero = (int) substr($derniereReference, -4);
            $nouveauNumero = $numero + 1;
        } else {
            $nouveauNumero = 1;
        }

        // Garantir l'unicité même en cas de concurrence ou fixtures existantes
        do {
            $reference = sprintf('FAC-%s-%04d', $annee, $nouveauNumero);
            if (!$this->factureRepository->referenceExists($reference)) {
                return $reference;
            }
            $nouveauNumero++;
        } while (true);
    }
}

