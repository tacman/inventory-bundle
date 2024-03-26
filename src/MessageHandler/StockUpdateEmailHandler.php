<?php

namespace PlinioCardoso\InventoryBundle\MessageHandler;

use PlinioCardoso\InventoryBundle\Entity\Product;
use PlinioCardoso\InventoryBundle\Entity\Stock;
use PlinioCardoso\InventoryBundle\Message\StockUpdateNotification;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class StockUpdateEmailHandler implements StockUpdateHandlerInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private MailerInterface $mailer,
        private ParameterBagInterface $parameterBag
    ) {}

    public function __invoke(StockUpdateNotification $message): void
    {
        $this->logger->info('Handling stock updated message - Email');
        $stock = $message->getContent();
        $product = $stock->getProduct();
        $this->logger->info(
            "Handling stock updated message - Stock ID: {$stock->getId()} - Quantity Added: {$stock->getQuantity()}"
        );

        if ($stock->getQuantity() <= 0) {
            $this->handleOutOfStockEmail($product, $stock);
        }
    }

    private function handleOutOfStockEmail(Product $product, Stock $stock): void
    {
        $this->logger->info("Product is out of stock, sending email to the supplier");

        $from = $this->parameterBag->get('inventory.out_of_stock_notification.from');
        $to = $this->parameterBag->get('inventory.out_of_stock_notification.to');
        $subject = $this->parameterBag->get('inventory.out_of_stock_notification.subject');

        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate('@Inventory/emails/out-of-stock.html.twig')
            ->context([
                'sku' => $product->getSku(),
                'warehouse' => $stock->getWarehouse()->getName(),
            ]);

        try {
            $this->logger->info("Sending email");
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error("ERROR sending email: {$e->getMessage()}");
        }
    }
}
