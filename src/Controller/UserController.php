<?php

namespace App\Controller;

use App\Entity\SubscriptionPayment;
use App\Entity\Subscription;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController extends AbstractController
{

    /**
     * @Route("/usersubscriptions", name="user_subscriptions")
     */
    public function userSubscriptions()
    {
        /**
         * Dummy data
         */
        $userId = 1; /* fake logged in user ID */

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)
            ->find($userId);

        /* @var \Doctrine\ORM\PersistentCollection $subscriptions */
        $subscriptions = $user->getSubscriptions();

        /* @var Subscription[] $userSubscriptionArray */
        $userSubscriptionArray = $subscriptions->toArray();

        return $this->render('user/user-subscriptions.html.twig', [
            'title' => 'UserController > userSubscriptions Action',
            'userSubscriptionArray' => $userSubscriptionArray,
        ]);
    }

    /**
     * @Route("/subscription/activate/{id}", methods={"GET"}, name="subscription_activate")
     */
    public function subscriptionActivate($id)
    {
        /*
        * check if user have privileges to perform action, and if not redirect ...
        * delete subscription
        */
        $em = $this->getDoctrine()->getManager();

        /* @var Subscription $subscription */
        $subscription = $em->getRepository(Subscription::class)->find($id);

        /*
         * Check if payment is after 37 days after last payment
         * If not allow to change status to active
         * Different approach is used is s command (app:cancel-inactive) - composed update select query
         */
        $daysLeftToSubsEnd = $em->getRepository(SubscriptionPayment::class)->daysLeftToSubsEnd($subscription->getId());
        /* var_dump($daysLeftToSubsEnd);die; */
        if($daysLeftToSubsEnd === NULL ){
            $this->addFlash('warning', 'Hey new guy! You have no payments yet! Did you forgot to make payment?');
        }elseif (is_int($daysLeftToSubsEnd) && $daysLeftToSubsEnd > 0){
            $subscription->setStatus('active');
            $em->persist($subscription);
            $em->flush();
            $this->addFlash('message', 'Subscription reactivated');
            if($daysLeftToSubsEnd <= 7) $this->addFlash('warning', 'Hey! Did you forgot to make payment? Your subscription will be canceled in '. $daysLeftToSubsEnd. ' days');
        }else{
            $this->addFlash('warning', 'Sorry your subscription period has expired! Make a new purchase.');
        }

        /* redirect to user panel */
        return new RedirectResponse($this->generateUrl("user_subscriptions"));
    }

    /**
     * @Route("/subscription/stop/{id}", methods={"GET"}, name="subscription_stop")
     */
    public function subscriptionStop($id)
    {
        /* check if user have privileges to perform action, and if not redirect ... */
        /* delete subscription */
        $em = $this->getDoctrine()->getManager();

        /* @var Subscription $subscription */
        $subscription = $em->getRepository(Subscription::class)->find($id);
        $subscription->setStatus('stopped');
        $em->persist($subscription);
        $em->flush();
        $this->addFlash('message', 'Subscription stopped');

        /* redirect to user panel */
        return new RedirectResponse($this->generateUrl("user_subscriptions"));
    }

//    /**
//     * @Route("/subscription/pay/{id}", methods={"GET"}, name="subscription_payment")
//     * TODO
//     */
//    public function subscriptionPayment(Request $request)
//    {
//        /* check if user have privileges to perform action, and if not redirect ... */
//        /* process payment form and redirect to user_subscriptions */
//
//        /* $em = $this->getDoctrine()->getManager(); */
//        /* $em->getRepository(Subscription::) */
//
//        /* redirect to user panel */
//        return new RedirectResponse($this->generateUrl("user_subscriptions"));
//    }

}
