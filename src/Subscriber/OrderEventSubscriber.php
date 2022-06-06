<?php

namespace App\Subscriber;

use App\Events\OrderEvent;
use App\Events\UserEvent;
use App\Services\Mail\MailerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class OrderEventSubscriber implements EventSubscriberInterface
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
            OrderEvent::REGISTER_ORDER_ACTION => 'onRegistered',
        ];
    }

    public function onRegistered(OrderEvent $event)
    {
        try {
            $template = 'emails/order/dispatched/dispatched.html.twig';
            $subject = "Order registered sucessfully";
            $this->mailerService->sendEmail($event->getUser(),$template, $subject, $event->getOrder());
        } catch (TransportExceptionInterface $e) {
            $this->logger->error($e->getMessage());
        }

        $this->logger->info('this user has been registered');
    }
}