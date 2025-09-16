<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\PushNotification;

use App\Shared\Domain\Contracts\Notification\PushNotification;
use App\Shared\Domain\Contracts\Notification\PushNotificationSenderInterface;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;

#[WithMonologChannel('firebase.notification')]
final class FirebaseNotifier implements PushNotificationSenderInterface
{
    public function __construct(
        private Messaging $messaging,
        private LoggerInterface $logger,
    ) {
    }

    public function push(PushNotification $notification): void
    {
        $cloudMessage = CloudMessage::new()
            ->withNotification(FirebaseNotification::create($notification->title, $notification->body))
            ->withData($notification->data)
        ;

        if (PushNotification::TYPE_TOPIC === $notification->type) {
            $cloudMessage = $cloudMessage->toTopic($notification->target);
        } elseif (PushNotification::TYPE_ACCOUNT === $notification->type) {
            $cloudMessage = $cloudMessage->toToken($notification->target);
        }

        $this->logger->info('Sending notification to firebase', [
            'notification' => $notification,
        ]);

        $result = $this->messaging->send($cloudMessage);

        $this->logger->info('Notification sent to firebase', [
            'result' => $result,
        ]);
    }

    public function pushMulticast(PushNotification $notification, array $devices): void
    {
        $cloudMessage = CloudMessage::new()
            ->withNotification(FirebaseNotification::create($notification->title, $notification->body))
            ->withData($notification->data)
        ;

        $this->logger->info('Sending notification to firebase', [
            'notification' => $notification,
            'devices' => $devices,
        ]);

        $result = $this->messaging->sendMulticast($cloudMessage, $devices);

        $this->logger->info('Notification sent to firebase', [
            'successes' => $result->successes()->count(),
            'failures' => $result->failures()->count(),
        ]);
    }

    public function subscribe(string $accountToken, string $topic): void
    {
        $this->logger->info('Subscribing to topic', [
            'accountToken' => $accountToken,
            'topic' => $topic,
        ]);

        $result = $this->messaging->subscribeToTopic($topic, $accountToken);

        $this->logger->info('Subscribed to topic', [
            'result' => $result,
        ]);
    }

    public function unsubscribe(string $accountToken, string $topic): void
    {
        $this->logger->info('Unsubscribing to topic', [
            'accountToken' => $accountToken,
            'topic' => $topic,
        ]);

        $result = $this->messaging->unsubscribeFromTopic($topic, $accountToken);

        $this->logger->info('Unsubscribed to topic', [
            'result' => $result,
        ]);
    }

    public static function getTopic(string $topic, string $id, string $locale): string
    {
        return str_replace(
            ['{id}', '{locale}'],
            [$id, $locale],
            $topic,
        );
    }
}
