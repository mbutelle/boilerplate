<?php

declare(strict_types=1);

namespace App\Security\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Security\Entity\User;
use App\Security\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UpdatePasswordProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        if (!$data instanceof User) {
            throw new \InvalidArgumentException(sprintf('Expected instance of %s, got %s', User::class, get_class($data)));
        }

        $data->password = $this->userPasswordHasher->hashPassword($data, $data->password);

        $this->userRepository->flush();

        return $data;
    }
}
