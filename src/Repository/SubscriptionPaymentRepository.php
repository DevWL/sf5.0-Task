<?php

namespace App\Repository;

use App\Entity\SubscriptionPayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SubscriptionPayment|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubscriptionPayment|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubscriptionPayment[]    findAll()
 * @method SubscriptionPayment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionPaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubscriptionPayment::class);
    }

    public function daysLeftToSubsEnd($id){
        $dql = "SELECT subPay.date 
            FROM App\Entity\SubscriptionPayment subPay 
            WHERE subPay.subscription = {$id} 
            ORDER BY subPay.date DESC";
        $query = $this->getEntityManager()->createQuery($dql)->setMaxResults(1)->getResult();

        /* @var \DateTime $lastPayment */
        $lastPayment = $query[0]['date'];
        $dateSubsEnds = $lastPayment->modify('+30 day');
        $dateNow = new \DateTime('NOW');

        echo "<pre>";
        var_dump($dateNow, $dateSubsEnds);
        $daysLeftToSubsEndDateTime = $dateSubsEnds->diff($dateNow);
        $daysLeftToSubsEnd = (int)$daysLeftToSubsEndDateTime->format('%a');

        return $daysLeftToSubsEnd;
    }

    // /**
    //  * @return SubscriptionPayment[] Returns an array of SubscriptionPayment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SubscriptionPayment
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
