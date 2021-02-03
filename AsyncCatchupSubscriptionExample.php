<?php
declare(strict_types=1);

require 'vendor/autoload.php';
require 'config/config.php';

use Amp\Loop;
use Amp\Promise;
use Amp\Success;
use Assert\Assert;
use Prooph\EventStore\Async\ClientConnectionEventArgs;
use Prooph\EventStore\Async\EventStoreCatchUpSubscription;
use Prooph\EventStore\CatchUpSubscriptionSettings;
use Prooph\EventStore\EndPoint;
use Prooph\EventStore\ResolvedEvent;
use Prooph\EventStoreClient\EventStoreConnectionFactory;
use Prooph\EventStore\Async\EventStoreStreamCatchUpSubscription;

require 'vendor/autoload.php';
require 'config/config.php';

$options = getopt('', ['stream:', 'checkpoint:']);
if (!isset($options['stream'])) {
    echo 'No --stream option set' . PHP_EOL;
    exit();
} else {
    $stream = $options['stream'];
}

if (!isset($options['checkpoint'])) {
    $checkpoint = null;
} else {
    $checkpoint = (int)$options['checkpoint'];
    Assert::that($checkpoint)->greaterOrEqualThan(0);
}

echo '--------------------------------------------------------------------------------' . PHP_EOL;
echo ' NOTE: If you want to run a category stream you MUST prefix it with `$ce-`!' . PHP_EOL;
echo '--------------------------------------------------------------------------------' . PHP_EOL;

Loop::run(function () use ($stream, $checkpoint, $config) {
    $eventStore = EventStoreConnectionFactory::createFromEndPoint(
        new EndPoint(
            $config['eventstore-async']['host'],
            $config['eventstore-async']['port']
        )
    );

    $eventStore->onConnected(function (ClientConnectionEventArgs $eventArgs): void {
        echo 'Connected to ' . $eventArgs->remoteEndPoint()->host() . PHP_EOL;
    });

    $eventStore->onClosed(function (): void {
        echo 'Connection closed' . PHP_EOL;
    });

    yield $eventStore->connectAsync();

    yield $eventStore->subscribeToStreamFromAsync(
        $stream,
        $checkpoint,
        CatchUpSubscriptionSettings::default(),
        function (
            EventStoreStreamCatchUpSubscription $subscription,
            ResolvedEvent $resolvedEvent
        ): Promise {
            $event = $resolvedEvent->event();
            if ($event === null) {
                //var_dump($resolvedEvent->originalEvent()->metadata());
                //die('TEST');
                return new Success();
            }

            echo 'Type:     ' . $resolvedEvent->event()->eventType() . PHP_EOL;
            echo 'Number:   ' . $resolvedEvent->event()->eventNumber() . PHP_EOL;
            echo 'Payload:  ' . $resolvedEvent->event()->data() . PHP_EOL;
            echo 'Metadata: ' . $resolvedEvent->event()->metadata() . PHP_EOL;
            echo '--------------------------------------------------------------------------------' . PHP_EOL;
            echo PHP_EOL;

            return new Success();
        },
        function (EventStoreCatchUpSubscription $subscription): void {
            echo 'Started live processing on ' . (string)$subscription->streamId() . PHP_EOL;
        },
        //new StdoutCatchUpSubscriptionDropped()
        function (EventStoreStreamCatchUpSubscription $subscription) {
            echo 'Connection dropped!' . PHP_EOL;
            echo PHP_EOL;
            echo 'Stream ID: ' . $subscription->streamId() . PHP_EOL;
            echo 'Last processed number: ' . $subscription->lastProcessedEventNumber() . PHP_EOL;
            echo 'Subscription name: ' . $subscription->subscriptionName() . PHP_EOL;
            echo PHP_EOL;
        }
    );
});
