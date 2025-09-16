<?php

declare(strict_types=1);

namespace App\Security\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Security\ApiResource\PasswordResetRequest;
use App\Security\Entity\User;
use App\Security\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordResetProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof PasswordResetRequest) {
            return $data;
        }

        /** @var User $user */
        $user = $this->userRepository->findOneBy(['resetToken' => $data->token]);

        if (!$user) {
            return $data;
        }

        $user->resetPassword();

        $this->userRepository->upgradePassword(
            $user,
            $this->passwordHasher->hashPassword($user, $data->password)
        );

        $this->entityManager->flush();

        return $data;
    }
}
