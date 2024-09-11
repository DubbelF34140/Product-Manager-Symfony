<?php

namespace App\Repository;

use App\Entity\Movement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movement>
 */
class MovementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movement::class);
    }

    //    /**
    //     * @return Movement[] Returns an array of Movement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Movement
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findBySearch(string $search)
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.products', 'p')
            ->leftJoin('p.productType', 'pt');

        if ($search) {
            $qb->andWhere('m.type LIKE :search OR p.serialNumber LIKE :search OR pt.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $qb->orderBy('m.date', 'DESC');

        return $qb->getQuery();
    }


}
