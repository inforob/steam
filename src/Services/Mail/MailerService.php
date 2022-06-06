<?php

namespace App\Services\Mail;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerService{

    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmail(User $user, string $template, string $subject, $context = null) : void
    {
        $email = (new TemplatedEmail())
            ->from('admin@steam.net')
            ->to(new Address($user->getEmail()))
            ->subject($subject)
            // path of the Twig template to render
            ->htmlTemplate($template)

            // pass variables (name => value) to the template
            ->context([
                'user' => $user,
                'context' => $context
            ]);

        $this->mailer->send($email);
    }

}