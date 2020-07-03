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
        // $objDateTime = new \DateTime('NOW');
        // $dateString = $objDateTime->format('Y-m-d H:i:s');

        // Get Subscription with id(1) and if car is valid then change "new" value to "active"
        $id = 0;
        try {
            $entityManager = $this->getDoctrine()->getManager();
            $subscription = $entityManager
                ->getRepository(Subscription::class)
                ->find($id);
            if('cars is valid' === 1){
                $subscription->setStatus("new");
                $entityManager->flush();
                $this->addFlash('message', 'Your subscription is now "active"');
                // send email here
            }else{
                $this->addFlash('message', 'Invalid card number');
            }

            if (!$subscription) {
                throw $this->createNotFoundException(
                    'No Subscription found for id '.$id
                );
            }

            $status = $subscription->getStatus();
        }catch (\Exception $e){
            $this->addFlash('message', 'Subscription id not found');
            $status = "Status not available";
        }



        return $this->render('index/index.html.twig', [
            'title' => $title . ' ' . $status,
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
