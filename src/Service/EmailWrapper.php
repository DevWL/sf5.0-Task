<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailWrapper
{
    public $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Sends fake emails
     */
    public function fakeSendEmail()
    {
        $email = (new Email())
            ->from('office@company.com')
            ->to('userEntityEmail@gmail.com')
            ->subject('Your Subscription is now active!')
            ->html('<p>Thank you for your payment. Your subscription is now active! ...</p>');
        // $this->mailer->send($email); // fake send!
    }
}