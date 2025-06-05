<?php

namespace App\Repository;

use App\Entity\PublicationsSharing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PublicationsSharing>
 */
class PublicationsSharingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicationsSharing::class);
    }

    public function findByTagName(string $tagName): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.tags', 't')
            ->where('t.tag_name = :tagName')
            ->setParameter('tagName', $tagName)
            ->getQuery()
            ->getResult();
    }
    
    // Function to findByApproximateTagName
    public function findByApproximateTagName(string $tagName): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.tags', 't')
            ->where('t.tag_name LIKE :tagName')
            ->setParameter('tagName', '%' . $tagName . '%')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return PublicationsSharing[] Returns an array of PublicationsSharing objects
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

    //    public function findOneBySomeField($value): ?PublicationsSharing
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
