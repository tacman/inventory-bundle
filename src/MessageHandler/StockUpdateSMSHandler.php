<?php

namespace PlinioCardoso\InventoryBundle\MessageHandler;

use PlinioCardoso\InventoryBundle\Message\StockUpdateNotification;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class StockUpdateSMSHandler implements StockUpdateHandlerInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private MailerInterface $mailer,
        private ParameterBagInterface $parameterBag
    ) {}

    public function __invoke(StockUpdateNotification $message): void
    {
        $this->logger->info('Handling stock updated message - SMS');
        // TODO - To be implemented

    }
}
