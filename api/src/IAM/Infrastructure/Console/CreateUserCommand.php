<?php

declare(strict_types=1);

namespace App\IAM\Infrastructure\Console;

use App\IAM\Application\Command\CreateUser\CreateUserCommand as CreateUser;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a user, optionally with the admin role',
)]
final class CreateUserCommand extends Command
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'The email of the user')
            ->addArgument('password', InputArgument::OPTIONAL, 'The password of the user')
            ->addOption('admin', null, InputOption::VALUE_NONE, 'Grant the admin role')
            ->setHelp(<<<'HELP'
                The <info>%command.name%</info> command creates a user:

                  <info>php %command.full_name%</info>

                You will be asked for the missing email and password.

                Arguments and options can be given directly:

                  <info>php %command.full_name% user@example.com 's3cret' --admin</info>
                HELP);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        if (!is_string($email) || '' === $email) {
            $email = $io->askQuestion($this->emailQuestion());
        }

        $password = $input->getArgument('password');
        if (!is_string($password) || '' === $password) {
            $password = $io->askQuestion($this->passwordQuestion());
        }

        $admin = true === $input->getOption('admin');

        // L'écriture passe par le bus : c'est lui qui porte la transaction, et le
        // handler est le même que celui de l'endpoint de création.
        $this->commandBus->dispatch(new CreateUser((string) $email, (string) $password, $admin));

        $io->success(sprintf('%s created: %s', $admin ? 'Admin' : 'User', $email));

        return Command::SUCCESS;
    }

    private function emailQuestion(): Question
    {
        $question = new Question('Please enter an email: ');
        $question->setValidator(static function (?string $answer): string {
            if (null === $answer || '' === trim($answer)) {
                throw new \RuntimeException('Email cannot be empty');
            }
            if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException('Invalid email format');
            }

            return $answer;
        });

        return $question;
    }

    private function passwordQuestion(): Question
    {
        $question = new Question('Please enter a password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $question->setValidator(static function (?string $answer): string {
            if (null === $answer || strlen($answer) < 8) {
                throw new \RuntimeException('Password must be at least 8 characters long');
            }

            return $answer;
        });

        return $question;
    }
}
