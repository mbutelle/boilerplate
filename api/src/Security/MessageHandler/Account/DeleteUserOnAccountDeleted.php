<?php

declare(strict_types=1);

namespace App\Security\MessageHandler\Account;

use App\Security\Repository\UserRepository;
use App\Shared\Infrastructure\Message\Account\AccountDeleted;
use App\Shared\Infrastructure\Message\AsMessageHandler;
use App\Shared\Infrastructure\Message\MessageHandlerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
#[WithMonologChannel('message_handler')]
class DeleteUserOnAccountDeleted implements MessageHandlerInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(AccountDeleted $accountDeleted): void
    {
        $user = $this->userRepository->findOneBy(['account' => $accountDeleted->account]);

        if (!$user) {
            return;
        }

        $this->logger->info('Deleting user on account deletion', [
            'account' => (string) $accountDeleted->account->getId(),
        ]);

        $this->userRepository->delete($user);
        $this->userRepository->flush();
    }
}
