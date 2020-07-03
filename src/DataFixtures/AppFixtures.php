<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Subscription;
use App\Entity\SubscriptionPayment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // user TABLE ----------------------------------------------------------------------------------------------
        $pass =  password_hash("secretphrase", PASSWORD_BCRYPT, ['cost' => 12]);

        $userEntries = [
            ["Rob", $pass, ["customer"], "rob@gmail.com"],
            ["Bob", $pass, ["customer"], "bob@gmail.com"],
            ["Ana", $pass, ["customer"], "ana@gmail.com"],
            ["Joy", $pass, ["customer"], "joy@gmail.com"]
        ];

        foreach ($userEntries as $userEntry){
            $user = new User();
            $user->setUsername($userEntry[0]);
            $user->setPassword($userEntry[1]);
            $user->setRoles($userEntry[2]);
            $user->setEmail($userEntry[3]);
            $manager->persist($user);
        }
        $manager->flush();

        // subscription TABLE --------------------------------------------------------------------------------------
        $now = new \DateTime('NOW');
         // $now = $objDateTime->format('Y-m-d H:i:s');

        $subscriptionEntries = [
            [1,1,1,1,"new",5,null,null,$now],
            [2,2,2,2,"active",2,new \DateTime('2017-04-01'),null,$now],
            [3,3,3,3,"active",7,new \DateTime('2017-04-15'),null,$now]
        ];

        foreach($subscriptionEntries as $subscriptionEntry) {
            // get User object where id = $subscriptionEntry[1]
            $user = $manager
                ->getRepository(User::class)
                ->find($subscriptionEntry[1]);
            if (!$user) {
                throw new \Exception(
                    'No User found for id '.$subscriptionEntry[1]
                );
            }

            // create Subscription object
            $subscription = new Subscription();
            $subscription->setUser($user);
            $subscription->setSubscriptionShippingAddressId($subscriptionEntry[2]);
            $subscription->setSubscriptionBillingAddressId($subscriptionEntry[3]);
            $subscription->setStatus($subscriptionEntry[4]);
            $subscription->setSubscriptionPackId($subscriptionEntry[5]);
            $subscription->setStartedAt($subscriptionEntry[6]);
            $subscription->setUpdatedAt($subscriptionEntry[7]);
            $subscription->setCreatedAt($subscriptionEntry[8]);
            $manager->persist($subscription);
        }
        $manager->flush();

        // subscription_payment TABLE ------------------------------------------------------------------------------
        $subscriptionPaymentEntries = [
            [1,2,2400,new \DateTime('2017-04-01'),null,$now],
            [2,2,1700,new \DateTime('2017-05-01'),null,$now],
            [3,3,3600,new \DateTime('2017-04-15'),null,$now]
        ];

        foreach($subscriptionPaymentEntries as $subscriptionPaymentEntry){
            // get Subscription object where id = $subscriptionPaymentEntry[1]
            $subscription = $manager
                ->getRepository(Subscription::class)
                ->find($subscriptionPaymentEntry[1]);
            if (!$subscription) {
                throw new \Exception(
                    'No Subscription found for id '.$subscriptionPaymentEntry[1]
                );
            }

            $subscriptionPayment = new SubscriptionPayment();
            $subscriptionPayment->setSubscription($subscription);
            $subscriptionPayment->setChargedAmount($subscriptionPaymentEntry[2]);
            $subscriptionPayment->setDate($subscriptionPaymentEntry[3]);
            $subscriptionPayment->setUpdatedAt($subscriptionPaymentEntry[4]);
            $subscriptionPayment->setCreatedAt($subscriptionPaymentEntry[5]);
            $manager->persist($subscriptionPayment);
        }
        $manager->flush();
    }
}