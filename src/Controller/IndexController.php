<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use App\Entity\SubscriptionPayment;
use App\Service\EmailWrapper;
use App\Entity\Subscription;
use App\Entity\User;
use App\Form\CCPayFormType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MailerInterface $mailer)
    {
        // Get Subscription with id(1) and if car is valid then change "new" value to "active"
        $id = 1;

        /*> FORM SETUP */
        $defaultData = ['message' => 'Type your message here'];

        $form = $this->createFormBuilder($defaultData)
            ->add('cardNumber', TextType::class, [
                'constraints' => new Length(['min' => 3]),
            ])
            ->add('cvvNumber', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3]),
                ],
            ])
            ->add('cardType', ChoiceType::class, ["choices"=> ["MasterCard" => "MS", "Visa" => "VI", "AmericanExpress"=> "AE"]])
            ->add('package', ChoiceType::class, ["choices"=> ["Basic" => 1500, "Standard" => 2300, "Pro"=> 3000]])
            ->add('send', SubmitType::class)
            ->getForm();

        /*< FORM SETUP */

        /*> FORM POPULATION */
//        $form->get('cardNumber')->setData(12345678912345678);
//        $form->get('cvvNumber')->setData(111);
//        $form->get('CardType')->setData();
        /*> FORM POPULATION */

        /*> FORM SUBMIT */
        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();
        }
        /*< FORM SUBMIT */

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
