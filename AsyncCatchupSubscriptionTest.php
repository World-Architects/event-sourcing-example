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
use Throwable;

require 'vendor/autoload.php';
require 'eventstore.php';

$options = getopt('', ['stream:', 'checkpoint:', 'connection:']);
if (!isset($options['stream'])) {
	echo 'No --stream option set' . PHP_EOL;
	exit();
} else {
	$stream = $options['stream'];
}

if (!isset($options['checkpoint'])) {
	$checkpoint = 0;
} else {
	$checkpoint = (int)$options['checkpoint'];
	Assert::that($checkpoint)->greaterOrEqualThan(0);
}

if (!isset($options['connection'])) {
	$connection = 'eventStoreAsync';
} else {
	$connection = $options['connection'];
}

if (!isset($options['verbose'])) {
	$verbose = true;
} else {
	$verbose = (bool)$options['verbose'];
}
echo '--------------------------------------------------------------------------------' . PHP_EOL;
echo ' NOTE: If you want to run a category stream you MUST prefix it with `$ce-`!' . PHP_EOL;
echo '--------------------------------------------------------------------------------' . PHP_EOL;

$connection = $eventStore;

Loop::run(function () use ($connection, $stream, $checkpoint) {
	yield $connection->subscribeToStreamFromAsync(
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
