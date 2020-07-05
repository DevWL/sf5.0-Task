<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use App\Entity\SubscriptionPayment;
use App\Service\EmailWrapper;
use App\Entity\Subscription;
use App\Entity\User;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MailerInterface $mailer)
    {
        // Get Subscription with id(1) and if car is valid then change "new" value to "active"
        $id = 1;

        try {
            $em = $this->getDoctrine()->getManager();
            $subscription = $em
                ->getRepository(Subscription::class)
                ->find($id);
            if('cars is valid' === 1){
                $subscription->setStatus("new");
                $em->flush();
                $this->addFlash('message', 'Your subscription is now active');
                $email = new EmailWrapper($mailer);
                $email->fakeSendEmail();
                $this->addFlash('message', 'Email sent');
            }else{
                $this->addFlash('message', 'Invalid card number');
            }

            if (!$subscription) {
                $this->addFlash('message', 'Subscription id not found');
                throw $this->createNotFoundException(
                    'No Subscription found for id '.$id
                );
            }

            $status = $subscription->getStatus();

        }catch (\Exception $e){
            $status = "Status is not available";
        }

        return $this->render('index/index.html.twig', [
            'title' => 'IndexController > index Action<br/>' . ' ' . $status,
            'form' => ''
        ]);
    }
}
