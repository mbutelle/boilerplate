<?php

declare(strict_types=1);

namespace App\IAM\Infrastructure\Message;

use App\IAM\Domain\Contract\Repository\UserRepositoryInterface;
use App\IAM\Infrastructure\Email\AccountCreatedEmailSender;
use App\Shared\Domain\Contracts\DomainId;
use App\Shared\Infrastructure\Message\AsMessageHandler;
use App\Shared\Infrastructure\Message\MessageHandlerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;

/**
 * Exécuté par le worker, dans un autre processus que la requête HTTP.
 */
#[AsMessageHandler]
#[WithMonologChannel('message_handler')]
final class SendAccountCreatedEmailHandler implements MessageHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private AccountCreatedEmailSender $sender,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(SendAccountCreatedEmail $message): void
    {
        $user = $this->repository->get(DomainId::fromString($message->userId));

        if (null === $user) {
            // Le compte a pu être supprimé entre la mise en file et la
            // consommation. On abandonne sans lever : relancer le message ne
            // ferait qu'échouer à nouveau jusqu'à épuisement des tentatives.
            $this->logger->warning('Compte introuvable, e-mail de création abandonné.', [
                'userId' => $message->userId,
            ]);

            return;
        }

        $this->sender->send($user);
    }
}
