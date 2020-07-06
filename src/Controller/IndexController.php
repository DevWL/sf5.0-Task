<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use App\Service\EmailWrapper;
use App\Entity\SubscriptionPayment;
use App\Entity\Subscription;
use App\Entity\User;
use App\Form\CCPayFormType;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MailerInterface $mailer, Request $request)
    {
        /**
         *  Dummy data
         */
        $subsID = 1; /* Task requirement! - Get Subscription with id == 1 */
        $userID = 1; /* Get User id == 1 */
        $date = new \DateTime('NOW'); /* Get Subscription with id(1) and if car is valid then change "new" value to "active" */

        /**
         * Create, validate and insert form data
         */
        $form = $this->createForm(CCPayFormType::class);
        $form->handleRequest($request);

        /**
         * Update Subscription (use dummy data)
         * Insert new SubscriptionPayment
         */
        if($form->isSubmitted() && $form->isValid()){
            /** @var CCPayFormType $cCPayFormData */
            $cCPayFormData = $form->getData();
            $cardType = $form->get('cardType')->getData();

            dump($cCPayFormData); //test
            dump($cardType); //test

            // render form again before any db insert
            // try to keep fields from submited form
            if($cardType){

            }

            /**
             * Assuming that Subscription does exists for that User before making payment
             * (otherwise we would need to create a new Subscription and set its status to "active" skipping "new" state)
             * - updating existing subscription AND adding new SubscriptionPayment for that Subscription
             */
            $em = $this->getDoctrine()->getManager();

            /* @var Subscription $subscription */
            $subscription = $em
                ->getRepository(Subscription::class)
                ->find($subsID); // dummy data

            $user = $em
                ->getRepository(User::class)
                ->find($userID); // dummy data

            /**
             * Setting only required fields
             */
            $subscription->setStatus("active");
            $subscription->setSubscriptionPackId(rand(10,100)); // dummy data
            $subscription->setUser($user);
            $subscription->getStartedAt();

            $newSubscriptionPayment = new SubscriptionPayment($subscription);
            $newSubscriptionPayment->setSubscription($subscription)
                ->setChargedAmount($cCPayFormData->getPackage()) // use submitted form data
                ->setDate($date)
                ->setCreatedAt($date);
            $em->persist($newSubscriptionPayment);
            $em->flush();

            $email = new EmailWrapper($mailer);
            $email->fakeSendEmail();

            $this->addFlash('message', 'Your subscription is now active');
            $this->addFlash('message', 'Email sent');

            // return $this->redirectToRoute('index');

        }else{
            /* Remove me */
            dump('form data not validated or sent');
            if (!$form->isSubmitted()) dump('form data not submitted');
            if ($form->isSubmitted()) {
                if (!$form->isValid()) dump('form data not Valid but was Submitted');
            };
        }

        return $this->render('index/index.html.twig', [
            'title' => 'IndexController > index Action',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/task", name="task")
     */
    public function task()
    {
        return $this->render('index/task.html.twig', [
            'title' => 'IndexController > task Action',
        ]);
    }
}
