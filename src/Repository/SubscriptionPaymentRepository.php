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

     /**
      * Find all inactive subscriptinos and stop them
      * @return void
      */

    // PURE SQL QUERY TO FIND ONLY NEWEST PAYMENT DATE
    // SELECT s.id, s.status, sp.id, MAX(sp.date) FROM subscription s RIGHT JOIN subscription_payment sp ON s.id = sp.subscription_id GROUP BY s.id

    /**
     * @param $value
     * @return void
     */
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
