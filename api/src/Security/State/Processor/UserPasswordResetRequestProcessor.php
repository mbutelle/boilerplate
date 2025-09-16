<?php

declare(strict_types=1);

namespace App\Security\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Security\ApiResource\PasswordResetRequest;
use App\Security\Email\Sender\PasswordResetRequestEmailSender;
use App\Security\Entity\User;
use App\Security\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserPasswordResetRequestProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private PasswordResetRequestEmailSender $emailSender,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof PasswordResetRequest) {
            return $data;
        }

        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => $data->email]);

        if (!$user) {
            return $data;
        }

        $user->requestReset();

        $this->entityManager->flush();
        $this->emailSender->send($user);

        return $data;
    }
}
