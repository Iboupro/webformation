<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Course>
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }


    /**
     * @return Course[]
     */
    public function findByCategorySlug(string $slug): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.categoryId', 'cat')
            ->addSelect('cat')
            ->andWhere('cat.slug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findForpaginationFilter():Query
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.categoryId', 'cat')
            ->addSelect('cat')
            ->leftJoin('c.levelId', 'l')
            ->addSelect('l')
            ->leftJoin('c.user', 'u')
            ->addSelect('u')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery();
    }


//    /**
//     * @return Course[] Returns an array of Course objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Course
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
