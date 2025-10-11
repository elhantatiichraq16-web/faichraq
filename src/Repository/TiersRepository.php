<?php

namespace App\Repository;

use App\Entity\Tiers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tiers>
 */
class TiersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tiers::class);
    }

    public function save(Tiers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Tiers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Recherche des tiers par nom ou email
     */
    public function search(string $term): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.nom LIKE :term OR t.email LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('t.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve un tiers par email
     */
    public function findByEmail(string $email): ?Tiers
    {
        return $this->createQueryBuilder('t')
            ->where('t.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

