<?php

namespace App\Subscriber\User;

use App\Events\UserEvent;
use App\Services\Mail\MailerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class EventsSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    private MailerService $mailerService;

    public function __construct(LoggerInterface $logger,MailerService $mailerService)
    {
        $this->logger = $logger;
        $this->mailerService = $mailerService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserEvent::REGISTER_ACTION => 'onRegistered',
            UserEvent::RESET_PASSWORD  => 'onResetPassword',
        ];
    }

    public function onRegistered(UserEvent $event)
    {
        try {
            $this->mailerService->sendEmail($event->getUser(),'emails/user/registration/signup.html.twig');
        } catch (TransportExceptionInterface $e) {
            $this->logger->error($e->getMessage());
        }

        $this->logger->info('this user has been registered');
    }

    public function onResetPassword(UserEvent $event)
    {
        try {
            $this->mailerService->sendEmail($event->getUser(), 'emails/user/reset/reset.html.twig');
        } catch (TransportExceptionInterface $e) {
            $this->logger->error($e->getMessage());
        }

        $this->logger->info('sended de access link');
    }
}