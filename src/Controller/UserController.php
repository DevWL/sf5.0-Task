<?php

namespace App\Controller;

use App\Entity\SubscriptionPayment;
use App\Entity\Subscription;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
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
         */

        $daysLeftToSubsEnd = $em->getRepository(SubscriptionPayment::class)->daysLeftToSubsEnd($subscription->getId());
        /* var_dump($daysLeftToSubsEnd);die; */
        if($daysLeftToSubsEnd > 0){
            $subscription->setStatus('active');
            $em->persist($subscription);
            $em->flush();
            $this->addFlash('message', 'Subscription reactivated');
            if($daysLeftToSubsEnd <= 7) $this->addFlash('warning', 'Did you forgot to make payment? Your subscription will be canceled in '. $daysLeftToSubsEnd. ' days');
        }else{
            $this->addFlash('warning', 'Sorry your subscription period has expired! Make a new purchase.');
        }

        /* redirect to user panel */
        return new RedirectResponse($this->generateUrl("user_subscriptions"));
    }

    /**
     * @Route("/subscription/pay/{id}", methods={"GET"}, name="subscription_payment")
     */
    public function subscriptionPayment(Request $request)
    {
        /* check if user have privileges to perform action, and if not redirect ... */
        /* process payment form and redirect to user_subscriptions */

        $em = $this->getDoctrine()->getManager();
        /* $em->getRepository(Subscription::) */

        /* redirect to user panel */
        return new RedirectResponse($this->generateUrl("user_subscriptions"));
    }

    /**
     * @Route("/usersubscriptions", name="user_subscriptions")
     */
    public function userSubscriptions()
    {
        $title = "userSubscriptions action";
        $loggedInUserId = 2; /* just assuming that we query id of logged in User */

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)
            ->find($loggedInUserId);

        /* @var \Doctrine\ORM\PersistentCollection $subscriptions */
        $subscriptions = $user->getSubscriptions();
        /* @var Subscription[] $userSubscriptionArray */
        $userSubscriptionArray = $subscriptions->toArray();

        return $this->render('user/userSubscriptions.html.twig', [
            'title' => $title,
            'userSubscriptionArray' => $userSubscriptionArray,
        ]);
    }
}
