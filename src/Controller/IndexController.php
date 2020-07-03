<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\SubscriptionPayment;
use App\Entity\Subscription;
use App\Entity\User;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $title = "index action";
//        $objDateTime = new \DateTime('NOW');
//        $dateString = $objDateTime->format('Y-m-d H:i:s');

        return $this->render('index/index.html.twig', [
            'title' => $title,
        ]);
    }

    /**
     * @Route("/usersubscriptions", name="userSubscriptions")
     */
    public function userSubscriptions()
    {
        $title = "userSubscriptions action";

        return $this->render('index/userSubscriptions.html.twig', [
            'title' => $title,
        ]);
    }
}
