<?php


namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CCPayFormType extends AbstractType
{
    private $cardNumber;
    private $cardNumberMS;
    private $cardNumberVI;
    private $cardNumberAE;
    private $cvvNumber;
    private $cardType;
    private $package;
    private $tempChoice;

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CCPayFormType::class,
            'allow_extra_fields' => true,
            // 'validation_groups' => false,
            // 'empty_data' => [];
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('cardType', ChoiceType::class, [
                'placeholder' => 'Select card',
                'choices'=> [
                    'MasterCard' => 'MS',
                    'Visa' => 'VI',
                    'AmericanExpress'=> 'AE',
                ],
            ])
            ->add('package', ChoiceType::class, [
                'choices'=> [
                    "Basic - 1500 zł" => 1500,
                    "Standard - 2300 zł" => 2300,
                    "Pro - 3000 zł"=> 3000
                ],
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Purchase subscription',
                'attr' => ['class' => 'btn-primary btn float-right']
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                /** @var CCPayFormType|Array $user */
                $user = $event->getData();
                $form = $event->getForm();

                //$cardType = $user->getCardType(); // POST_SUBMIT
                //$cardType = $user['cardType']; // PRE_SUBMIT

                $this->getSelectedCardChoice($form, $user['cardType']); // render another input field based on dynamic value passed

                // dump($user);
                // dump($form);
            });


    }

    /**
     * @param string|null $choiceValue
     * This method should take dynamic value from submitted form and based of that it adds field with requested card type.
     * Just need to figure out how to get this value to the FormType class.
     */
    private function getSelectedCardChoice($form, string $choiceValue = null)
    {

        if($choiceValue) {
            $form->add('cvvNumber', IntegerType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3, 'max' => 7]),
                ],
            ]);
        }

        // this fields should be dynamically selected based on the choice dropdown!
        $choiceValue = $choiceValue ?? "MS";
        switch ($choiceValue) {
            case "MS":
                $form->add('cardNumberMS', IntegerType::class, [
                    'label' => 'MasterCard',
                    'constraints' => new Length(['min' => 16, 'max' => 16]),
                    'required' => false, // remove after testing
                    'constraints' => [
                        new NotBlank(),
                        new Regex([
                            'pattern'   => '/^5[1-5]\d{14}$/', // we should also validate checksum === 10 (We should create custom Validation Constrain) and implement $this->>luhnCheck() functionality
                            'match'     => true,
                            'message'   => 'Nieprawidłowy numer karty MasterCard'
                        ])
                    ]
                ]);
                break;
            case "VI":
                $form->add('cardNumberVI', IntegerType::class, [
                    'label' => 'VISA',
                    'constraints' => new Length(['min' => 13, 'max' => 16]),
                    'required' => false, // remove after testing
                    'constraints' => [
                        new NotBlank(),
                        new Regex([
                            'pattern'   => '/^4\d{12}(?:\d{3})?$/',
                            'match'     => true,
                            'message'   => 'Nieprawidłowy numer karty VISA'
                        ])
                    ]
                ]);
                break;
            case "AE":
                $form->add('cardNumberAE', IntegerType::class, [
                    'label' => 'American Express',
                    'constraints' => new Length(['min' => 15, 'max' => 15]),
                    'required' => false, // remove after testing
                    'constraints' => [
                        new NotBlank(),
                        new Regex([
                            'pattern'   => '/^3[47]\d{13}$/',
                            'match'     => true,
                            'message'   => 'Nieprawidłowy numer karty American Express'
                        ])
                    ]
                ]);
                break;

            $form->getForm();

        }
    }

    /**
     *
     * Luhn algorithm number checker - Author: (c) 2005-2008 shaman - www.planzero.org
     * (i) This function is not used in this project but if I would ue it.... I would probably create custom Assert class for form validation.
     *
     * @param $cardNum
     * @param int $checkSum
     * @return bool
     */
    private function luhnCheck($cardNum, $checkSum = 10) {

        // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
        $cardNum=preg_replace('/\D/', '', $cardNum);

        if($cardNum === '') return false;

        // Set the string length and parity
        $cardNum_length=strlen($cardNum);
        $parity=$cardNum_length % 2;

        // Loop through each digit and do the maths
        $total=0;
        for ($i=0; $i<$cardNum_length; $i++) {
        $digit=$cardNum[$i];
        // Multiply alternate digits by two
        if ($i % 2 == $parity) {
            $digit*=2;
            // If the sum is two digits, add them together (in effect)
            if ($digit > 9) {
            $digit-=9;
            }
        }
        // Total up the digits
        $total += $digit;
        }

        // If the total mod 10 equals 0, the number is valid
        return ($total % $checkSum == 0) ? TRUE : FALSE;
    }


/**
     * @return mixed
     */
    public function getTempChoice()
    {
        return $this->tempChoice;
    }

    /**
     * @param mixed $tempChoice
     * @return CCPayFormType
     */
    public function setTempChoice($tempChoice)
    {
        $this->tempChoice = $tempChoice;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * @param mixed $cardNumber
     * @return CCPayFormType
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getCardNumberMs()
    {
        return $this->cardNumberMS;
    }

    /**
     * @param mixed $cardNumberMS
     * @return CCPayFormType
     */
    public function setCardNumberMs($cardNumberMS)
    {
        $this->cardNumberMS = $cardNumberMS;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCardNumberVI()
    {
        return $this->cardNumberVI;
    }

    /**
     * @param mixed $cardNumberVI
     * @return CCPayFormType
     */
    public function setCardNumberVI($cardNumberVI)
    {
        $this->cardNumberVI = $cardNumberVI;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCardNumberAE()
    {
        return $this->cardNumberAE;
    }

    /**
     * @param mixed $cardNumberAE
     * @return CCPayFormType
     */
    public function setCardNumberAE($cardNumberAE)
    {
        $this->cardNumberAE = $cardNumberAE;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCvvNumber()
    {
        return $this->cvvNumber;
    }

    /**
     * @param mixed $cvvNumber
     * @return CCPayFormType
     */
    public function setCvvNumber($cvvNumber)
    {
        $this->cvvNumber = $cvvNumber;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCardType()
    {
        return $this->cardType;
    }

    /**
     * @param mixed $cardType
     * @return CCPayFormType
     */
    public function setCardType($cardType)
    {
        $this->cardType = $cardType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @param mixed $package
     * @return CCPayFormType
     */
    public function setPackage($package)
    {
        $this->package = $package;
        return $this;
    }

}