<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Message;

/**
 * Les bus commande, requête et événement avaient chacun leur interface ; le bus
 * de message n'avait que son implémentation Messenger, si bien qu'un abonné
 * devait dépendre du concret pour publier un message.
 */
interface MessageBusInterface
{
    public function dispatch(MessageInterface $message): void;
}
