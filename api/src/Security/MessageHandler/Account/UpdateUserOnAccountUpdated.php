<?php

declare(strict_types=1);

namespace App\Security\MessageHandler\Account;

use App\Security\Repository\UserRepository;
use App\Shared\Infrastructure\Message\Account\AccountUpdated;
use App\Shared\Infrastructure\Message\AsMessageHandler;
use App\Shared\Infrastructure\Message\MessageHandlerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
#[WithMonologChannel('message_handler')]
class UpdateUserOnAccountUpdated implements MessageHandlerInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(AccountUpdated $accountUpdated): void
    {
        $user = $this->userRepository->findOneBy(['account' => $accountUpdated->account]);

        if (!$user) {
            return;
        }

        $this->logger->info('Updating user on account updated', [
            'account' => (string) $accountUpdated->account->getId(),
        ]);

        $user->email = $accountUpdated->account->getEmail();
        $user->username = $accountUpdated->account->getEmail();

        $this->userRepository->flush();
    }
}
