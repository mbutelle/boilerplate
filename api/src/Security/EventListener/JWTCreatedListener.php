<?php

declare(strict_types=1);

namespace App\Security\EventListener;

use App\Security\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener('lexik_jwt_authentication.on_jwt_created')]
class JWTCreatedListener
{
    public function __invoke(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $payload = $event->getData();
        $payload['user_id'] = $user->id;

        $event->setData($payload);
    }
}
