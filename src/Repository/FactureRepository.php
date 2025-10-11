<?php

namespace App\Repository;

use App\Entity\Facture;
use App\Entity\Tiers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Facture>
 */
class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }

    public function save(Facture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Facture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Recherche des factures avec filtres
     */
    public function search(?string $term = null, ?Tiers $client = null, ?string $etat = null, ?\DateTime $dateDebut = null, ?\DateTime $dateFin = null): array
    {
        $qb = $this->createQueryBuilder('f')
            ->leftJoin('f.client', 'c')
            ->addSelect('c');

        if ($term) {
            $qb->andWhere('f.reference LIKE :term OR c.nom LIKE :term')
                ->setParameter('term', '%' . $term . '%');
        }

        if ($client) {
            $qb->andWhere('f.client = :client')
                ->setParameter('client', $client);
        }

        if ($etat) {
            $qb->andWhere('f.etat = :etat')
                ->setParameter('etat', $etat);
        }

        if ($dateDebut) {
            $qb->andWhere('f.dateFacture >= :dateDebut')
                ->setParameter('dateDebut', $dateDebut);
        }

        if ($dateFin) {
            $qb->andWhere('f.dateFacture <= :dateFin')
                ->setParameter('dateFin', $dateFin);
        }

        return $qb->orderBy('f.dateFacture', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les factures non payées
     */
    public function findNonPayees(): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.etat != :etatPayee')
            ->setParameter('etatPayee', Facture::ETAT_PAYEE)
            ->orderBy('f.dateFacture', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Calcule le chiffre d'affaires du mois courant
     */
    public function getChiffreAffairesMoisCourant(): float
    {
        $debutMois = new \DateTime('first day of this month');
        $finMois = new \DateTime('last day of this month');

        $result = $this->createQueryBuilder('f')
            ->select('SUM(f.totalTtc)')
            ->where('f.dateFacture >= :debutMois')
            ->andWhere('f.dateFacture <= :finMois')
            ->andWhere('f.etat = :etatPayee')
            ->setParameter('debutMois', $debutMois)
            ->setParameter('finMois', $finMois)
            ->setParameter('etatPayee', Facture::ETAT_PAYEE)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    /**
     * Compte le nombre de factures non payées
     */
    public function countNonPayees(): int
    {
        return $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->where('f.etat != :etatPayee')
            ->setParameter('etatPayee', Facture::ETAT_PAYEE)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Trouve les 5 dernières factures
     */
    public function findDernieresFactures(int $limit = 5): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.client', 'c')
            ->addSelect('c')
            ->orderBy('f.dateFacture', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve la dernière référence de facture
     */
    public function findDerniereReference(): ?string
    {
        $result = $this->createQueryBuilder('f')
            ->select('f.reference')
            ->orderBy('f.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result ? $result['reference'] : null;
    }
}

