<?php

namespace App;

use App\Shared\Application\Command\AsCommandHandler;
use App\Shared\Application\Event\AsEventHandler;
use App\Shared\Application\Query\AsQueryHandler;
use App\Shared\Infrastructure\Message\AsMessageHandler;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(AsQueryHandler::class, static function (ChildDefinition $definition): void {
            $definition->addTag('messenger.message_handler', ['bus' => 'query.bus']);
        });

        $container->registerAttributeForAutoconfiguration(AsCommandHandler::class, static function (ChildDefinition $definition): void {
            $definition->addTag('messenger.message_handler', ['bus' => 'command.bus']);
        });

        $container->registerAttributeForAutoconfiguration(AsEventHandler::class, static function (ChildDefinition $definition): void {
            $definition->addTag('messenger.message_handler', ['bus' => 'event.bus']);
        });

        $container->registerAttributeForAutoconfiguration(AsMessageHandler::class, static function (ChildDefinition $definition): void {
            $definition->addTag('messenger.message_handler', ['bus' => 'message.bus']);
        });
    }
}
