<?php

declare(strict_types=1);

namespace App\Security\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Security\ApiResource\AdminCreationRequest;
use App\Security\Email\Sender\AdminRequestEmailSender;
use App\Security\Entity\User;
use App\Security\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAdminProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private AdminRequestEmailSender $emailSender,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    /**
     * @return User
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof AdminCreationRequest) {
            throw new \InvalidArgumentException(sprintf('Expected instance of %s, got %s', AdminCreationRequest::class, get_debug_type($data)));
        }

        $existing = $this->userRepository->findOneBy(['email' => $data->email]);
        if ($existing instanceof User) {
            // For minimal behavior, prevent duplicate creation
            throw new \RuntimeException('User already exists');
        }

        $user = User::createAdmin($data->email);
        $user->password = $this->userPasswordHasher->hashPassword($user, $data->email);
        // Force reset flow for initial password setup
        $user->requestReset();

        $this->userRepository->save($user);
        $this->entityManager->flush();

        // Send reset email with admin-specific URL
        $this->emailSender->send($user);

        return $user;
    }
}
