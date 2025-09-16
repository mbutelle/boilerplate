<?php

declare(strict_types=1);

namespace App\Shared\Domain\Contracts\Notification;

class PushNotification
{
    public const string TYPE_TOPIC = 'topic';
    public const string TYPE_ACCOUNT = 'account';

    /**
     * @param array<string,string> $data
     */
    public function __construct(
        public string $title,
        public string $body,
        public array $data,
        public string $type,
        public ?string $target = null,
    ) {
    }
}
