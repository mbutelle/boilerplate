<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\ApiPlatform\Resource\Payload\CommandableInterface;
use App\Shared\Infrastructure\ApiPlatform\Resource\ResourceInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @implements ProcessorInterface<CommandInterface|CommandableInterface,ResourceInterface>
 */
final class ApplyCommandProcessor implements ProcessorInterface
{
    /**
     * @param array<string,string> $mapping
     */
    public function __construct(
        private CommandBusInterface $commandBus,
        private Security $security,
        #[Autowire('%domain_resource_mapping%')]
        private array $mapping,
    ) {
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?ResourceInterface
    {
        $context['security'] = $this->security;
        $command = $this->getCommand($data, $operation, $uriVariables, $context);
        $model = $this->commandBus->dispatch($command);

        if (null === $model) {
            return null;
        }

        $modelClass = str_replace('Proxies\\__CG__\\', '', get_class($model));
        if (empty($this->mapping[$modelClass])) {
            throw new \LogicException('No mapping found for '.$modelClass);
        }

        $resourceClass = $this->mapping[$modelClass];

        if (!is_a($resourceClass, ResourceInterface::class, true)) {
            throw new \LogicException('Resource class must implement '.ResourceInterface::class);
        }

        return ($resourceClass)::fromDomain($model);
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     */
    private function getCommand(mixed $data, Operation $operation, array $uriVariables, array $context): CommandInterface
    {
        $commandClass = $context['request']?->attributes?->get('commandable');
        if ($data instanceof CommandInterface) {
            $command = $data;
        } elseif ($data instanceof CommandableInterface) {
            $command = $data->toCommand($uriVariables, $context);
        } elseif ($data instanceof ResourceInterface && $operation instanceof Delete) {
            $command = $data->getDeleteCommand();

            if (null === $command) {
                throw new \LogicException('No delete command found for resource');
            }
        } elseif ($commandClass && is_a($commandClass, CommandableInterface::class, true)) {
            $command = (new $commandClass())->toCommand($uriVariables, $context);
        } else {
            throw new \LogicException('Unsupported data type', 409);
        }

        return $command;
    }
}
