<?php

declare(strict_types=1);

namespace App\Shared\Domain\Contracts\Notification;

interface PushNotificationSenderInterface
{
    public const TOPIC_AUCTION_BID = 'auction-bid-{id}-{locale}';

    public function push(PushNotification $notification): void;

    public function subscribe(string $accountToken, string $topic): void;

    public function unsubscribe(string $accountToken, string $topic): void;

    public static function getTopic(string $topic, string $id, string $locale): string;

    public function pushMulticast(PushNotification $notification, array $devices): void;
}
