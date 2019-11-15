<?php
declare(strict_types=1);

namespace Prooph\EventStoreClient;

use Amp\Loop;
use Amp\Promise;
use Amp\Success;
use Assert\Assert;
use Prooph\EventStore\Async\CatchUpSubscriptionDropped;
use Prooph\EventStore\Async\ClientConnectionEventArgs;
use Prooph\EventStore\Async\EventAppearedOnCatchupSubscription;
use Prooph\EventStore\Async\EventStoreCatchUpSubscription;
use Prooph\EventStore\Async\LiveProcessingStartedOnCatchUpSubscription;
use Prooph\EventStore\CatchUpSubscriptionSettings;
use Prooph\EventStore\ResolvedEvent;
use Prooph\EventStore\SubscriptionDropReason;
use Prooph\EventStore\EndPoint;
use Prooph\EventStoreClient\EventStoreConnectionFactory;
use Throwable;

require 'vendor/autoload.php';

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

Loop::run(function () use ($stream, $checkpoint) {
	$eventStore = EventStoreConnectionFactory::createFromEndPoint(
		new EndPoint('127.0.0.1', 1113)
	);

	$eventStore->onConnected(function (): void {
		echo 'Connected' . PHP_EOL;
	});

	$eventStore->onClosed(function (): void {
		echo 'Connection closed' . PHP_EOL;
	});

	yield $eventStore->connectAsync();

	yield $eventStore->subscribeToStreamFromAsync(
		$stream,
		$checkpoint,
		CatchUpSubscriptionSettings::default(),

		new class implements EventAppearedOnCatchupSubscription {
			public function __invoke(
				EventStoreCatchUpSubscription $subscription,
				ResolvedEvent $resolvedEvent
			): Promise {
				//var_dump($resolvedEvent);
				echo 'Type:     ' . $resolvedEvent->event()->eventType() . PHP_EOL;
				echo 'Number:   ' . $resolvedEvent->event()->eventNumber() . PHP_EOL;
				echo 'Payload:  ' . $resolvedEvent->event()->data() . PHP_EOL;
				echo 'Metadata: ' . $resolvedEvent->event()->metadata() . PHP_EOL;
				echo '--------------------------------------------------------------------------------' . PHP_EOL;
				echo PHP_EOL;

				return new Success();
			}
		},

		new class implements LiveProcessingStartedOnCatchUpSubscription {
			public function __invoke(EventStoreCatchUpSubscription $subscription): void {
				echo 'Started live processing on ' . (string)$subscription->streamId() . PHP_EOL;
			}
		},

		new class implements CatchUpSubscriptionDropped {
			public function __invoke(
				EventStoreCatchUpSubscription $subscription,
				SubscriptionDropReason $reason,
				Throwable $exception = null
			): void {
				echo $reason . PHP_EOL;
			}
		}
	);
});
