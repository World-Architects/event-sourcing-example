<?php
declare(strict_types=1);

namespace Prooph\EventStoreClient;

use Amp\Loop;
use Assert\Assert;
use Prooph\EventStore\Async\EventStoreCatchUpSubscription;
use Prooph\EventStore\Async\LiveProcessingStartedOnCatchUpSubscription;
use Prooph\EventStore\CatchUpSubscriptionSettings;
use Prooph\EventStore\EndPoint;
use Psa\EventSourcing\Projection\Async\StdoutCatchupSubscription;
use Psa\EventSourcing\Projection\Async\StdoutCatchUpSubscriptionDropped;

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
		new StdoutCatchupSubscription(),
		new class implements LiveProcessingStartedOnCatchUpSubscription {
			public function __invoke(EventStoreCatchUpSubscription $subscription): void {
				echo 'Started live processing on ' . (string)$subscription->streamId() . PHP_EOL;
			}
		},
		new StdoutCatchUpSubscriptionDropped()
	);
});
