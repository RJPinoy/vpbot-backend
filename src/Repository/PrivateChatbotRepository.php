<?php

namespace App\Repository;

use App\Entity\PrivateChatbot;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PrivateChatbot>
 */
class PrivateChatbotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrivateChatbot::class);
    }

    public function findWithAssistantsByUserId(int $userId): ?PrivateChatbot
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.assistants', 'a')
            ->addSelect('a')
            ->leftJoin('c.userChatbot', 'u') // Join the user
            ->andWhere('u.id = :userId')     // Filter on the joined user's ID
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return PrivateChatbot[] Returns an array of PrivateChatbot objects
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

//    public function findOneBySomeField($value): ?PrivateChatbot
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
