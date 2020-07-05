<?php

namespace App\Entity;

use App\Repository\SubscriptionPaymentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubscriptionPaymentRepository::class)
 */
class SubscriptionPayment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Subscription::class, inversedBy="subscriptionPayments")
     */
    private $subscription;

    /**
     * @ORM\Column(type="integer")
     */
    private $charged_amount;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }

    public function getChargedAmount(): ?int
    {
        return $this->charged_amount;
    }

    public function setChargedAmount(int $charged_amount): self
    {
        $this->charged_amount = $charged_amount;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at = NULL): self
    {
        if($updated_at) {
            $this->updated_at = $updated_at;
        }else{
            $this->updated_at = new \DateTime('NOW');
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at = NULL): self
    {
        if($created_at) {
            $this->updated_at = $updated_at;
        }else{
            $this->updated_at = new \DateTime('NOW');
        }

        return $this;
    }
}
