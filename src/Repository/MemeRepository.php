<?php

namespace App\Repository;

use App\Entity\Meme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Meme>
 */
class MemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meme::class);
    }

    public function save(Meme $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Meme $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Recherche simple par titre/légende
     */
    public function search(?string $term = null): array
    {
        $qb = $this->createQueryBuilder('m');
        if ($term) {
            $qb->andWhere('m.title LIKE :term OR m.caption LIKE :term')
               ->setParameter('term', '%' . $term . '%');
        }
        return $qb->orderBy('m.createdAt', 'DESC')
                  ->getQuery()
                  ->getResult();
    }
}
