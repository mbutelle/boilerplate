<?php

declare(strict_types=1);

namespace App\Security\MessageHandler\Account;

use App\Security\Entity\User;
use App\Security\Repository\UserRepository;
use App\Shared\Infrastructure\Message\Account\AccountCreated;
use App\Shared\Infrastructure\Message\AsMessageHandler;
use App\Shared\Infrastructure\Message\MessageHandlerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
#[WithMonologChannel('message_handler')]
class CreateUserOnAccountCreation implements MessageHandlerInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private LoggerInterface $logger,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function __invoke(AccountCreated $accountCreated): void
    {
        if ($this->userRepository->count(['email' => $accountCreated->email])) {
            throw new \LogicException('User already exists');
        }

        $this->logger->info('Creating user on account creation');

        $user = new User(
            null,
            $accountCreated->email,
            [
                User::ROLE_ACCOUNT,
            ],
            null,
            $accountCreated->email,
            $accountCreated->account,
        );

        $this->userRepository->upgradePassword(
            $user,
            $this->userPasswordHasher->hashPassword($user, $accountCreated->password),
        );

        $this->userRepository->save($user);
        $this->userRepository->flush();
    }
}
