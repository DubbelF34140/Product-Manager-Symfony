<?php

namespace App\Repository;

use App\Entity\ProductType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductType>
 */
class ProductTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductType::class);
    }

    //    /**
    //     * @return ProductType[] Returns an array of ProductType objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ProductType
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findBySearchTerm(float|bool|int|string|null $search)
    {
        $queryBuilder = $this->createQueryBuilder('p');

        if ($search) {
            $queryBuilder
                ->andWhere('p.name LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $search . '%')
                ->orderBy('p.name', 'ASC');
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    public function findBySearchTermAndCategory(?string $search, ?string $category)
    {
        $qb = $this->createQueryBuilder('pt');

        if ($search) {
            $qb->andWhere('pt.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($category) {
            $qb->join('pt.category', 'c')
                ->andWhere('c.id = :category')
                ->setParameter('category', $category);
        }

        return $qb->getQuery();
    }


}
