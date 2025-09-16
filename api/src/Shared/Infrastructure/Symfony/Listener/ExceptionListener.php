<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Listener;

use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener]
#[WithMonologChannel('exception')]
class ExceptionListener
{
    public function __construct(
        #[Autowire('%kernel.environment%')]
        public string $env,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $ex = $event->getThrowable();

        $code = !empty($ex->getCode()) ? $ex->getCode() : 500;
        $error = [
            'message' => $ex->getMessage(),
        ];

        if ('dev' === $this->env) {
            $error['code'] = $code;
            $error['file'] = $ex->getFile();
            $error['line'] = $ex->getLine();
        }

        $this->logger->error($ex->getMessage(), [
            'exception' => $ex,
        ]);

        $event->setResponse(new JsonResponse($error, $code < 400 || $code > 599 ? 500 : $code));
    }
}
