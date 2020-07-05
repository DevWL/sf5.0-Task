<?php


namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CCPayFormType extends AbstractType
{
    private $cardNumber;
    private $cvvNumber;
    private $cardType;
    private $package;

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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CCPayFormType::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cardNumber', IntegerType::class, [
                'constraints' => new Length(['min' => 16]),
            ])
            ->add('cvvNumber', IntegerType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3, 'max'=> 4]),
                ],
            ])
            ->add('cardType', ChoiceType::class, ["choices"=> ["MasterCard" => "MS", "Visa" => "VI", "AmericanExpress"=> "AE"]])
            ->add('package', ChoiceType::class, ["choices"=> ["Basic" => 1500, "Standard" => 2300, "Pro"=> 3000]])
            ->add('send', SubmitType::class)
        ;
    }

}