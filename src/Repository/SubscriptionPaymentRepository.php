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

    /**
     *
     * This function returns number of days left to the end of the subscription
     *
     * @param $id
     * @return int|NULL (days) or NULL
     */
    public function daysLeftToSubsEnd($id){
        $dql = "SELECT subPay.date 
            FROM App\Entity\SubscriptionPayment subPay 
            WHERE subPay.subscription = {$id} 
            ORDER BY subPay.date DESC";
        $query = $this->getEntityManager()->createQuery($dql)->setMaxResults(1)->getResult();
        if($query === []){
            return NULL;
        }

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
}
