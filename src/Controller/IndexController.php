<?php

namespace App\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\SubscriptionPayment;
use App\Entity\Subscription;
use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MailerInterface $mailer)
    {
        $title = "index action";
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
                // send email here
                $email = (new Email())
                    ->from('office@company.com')
                    ->to('userEntityEmail@gmail.com')
                    ->subject('Your Subscription is now active!')
                    ->html('<p>Thank you for your payment. Your subscription is now active! ...</p>');
                // $mailer->send($email);
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
            'title' => $title . ' ' . $status,
        ]);
    }

    /**
     * @Route("/usersubscriptions", name="user_subscriptions")
     */
    public function userSubscriptions()
    {
        $title = "userSubscriptions action";
        $loggedInUserId = 2; // just assuming that we query id of logged in User

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)
            ->find($loggedInUserId);

        /* @var \Doctrine\ORM\PersistentCollection $subscriptions */
        $subscriptions = $user->getSubscriptions();
        /* @var Subscription[] $userSubscriptionArray */
        $userSubscriptionArray = $subscriptions->toArray();

        return $this->render('index/userSubscriptions.html.twig', [
            'title' => $title,
            'userSubscriptionArray' => $userSubscriptionArray,
        ]);
    }
}
