<?php

declare(strict_types=1);

namespace App\Security\Command;

use App\Security\Entity\User;
use App\Security\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a new user with Admin or Club Manager role',
)]
final class CreateUserCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'The email of the user')
            ->addArgument('password', InputArgument::OPTIONAL, 'The password of the user')
            ->addOption('admin', null, InputOption::VALUE_NONE, 'Create an admin user')
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> command creates a new user with Admin or Club Manager role:

  <info>php %command.full_name%</info>

You will be interactively asked for a email, password, and role.

You can also specify the email, and password as arguments:

  <info>php %command.full_name% user@example.com password</info>

To create an Admin user:

  <info>php %command.full_name% --admin</info>

To create a Club Manager user:

  <info>php %command.full_name% --club-manager</info>

You can combine arguments and options:

  <info>php %command.full_name% user@example.com password --admin</info>
HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Get or ask for email
        $email = $input->getArgument('email');
        if (!$email) {
            $question = new Question('Please enter an email: ');
            $question->setValidator(function ($answer) {
                if (empty($answer)) {
                    throw new \RuntimeException('Email cannot be empty');
                }
                if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                    throw new \RuntimeException('Invalid email format');
                }

                return $answer;
            });
            $email = $io->askQuestion($question);
        }

        // Check if user with this email already exists
        $existingUser = $this->userRepository->findOneBy(['email' => $email]);
        if ($existingUser) {
            $io->error(sprintf('User with email "%s" already exists.', $email));

            return Command::FAILURE;
        }

        // Get or ask for password
        $password = $input->getArgument('password');
        if (!$password) {
            $question = new Question('Please enter a password: ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $question->setValidator(function ($answer) {
                if (empty($answer)) {
                    throw new \RuntimeException('Password cannot be empty');
                }
                if (strlen($answer) < 8) {
                    throw new \RuntimeException('Password must be at least 8 characters long');
                }

                return $answer;
            });
            $password = $io->askQuestion($question);
        }

        // Determine role
        if ($input->getOption('admin')) {
            $userType = 'Admin';
        } elseif ($input->getOption('club-manager')) {
            $userType = 'Club Manager';
        } else {
            // If no role option is provided, ask for role
            $question = new Question('Please choose a role (1: Admin, 2: Club Manager): ', '1');
            $roleChoice = $io->askQuestion($question);

            if ('1' === $roleChoice) {
                $userType = 'Admin';
            } else {
                $userType = 'Club Manager';
            }
        }

        if ('Admin' === $userType) {
            $user = User::createAdmin($email);
        }

        $user->password = $this->passwordHasher->hashPassword($user, $password);
        // Create user

        // Hash the password
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->password = $hashedPassword;

        // Save user
        $this->userRepository->save($user);
        $this->userRepository->flush();

        $io->success(sprintf('%s user created successfully: %s', $userType, $email));

        return Command::SUCCESS;
    }
}
