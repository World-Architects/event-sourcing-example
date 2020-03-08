<?php
require 'vendor/autoload.php';
require 'config/config.php';

use Prooph\EventStoreHttpClient\EventStoreConnectionFactory;
use Prooph\EventStoreHttpClient\ConnectionSettings;
use Prooph\EventStore\EndPoint;
use Prooph\EventStore\UserCredentials;
use Prooph\EventStore\Transport\Http\EndpointExtensions;
use Prooph\EventStore\EventAppearedOnCatchupSubscription;
use Prooph\EventStore\EventStoreCatchUpSubscription;
use Prooph\EventStore\ResolvedEvent;
use Prooph\EventStore\LiveProcessingStartedOnCatchUpSubscription;
use Prooph\EventStore\CatchUpSubscriptionDropped;
use Prooph\EventStore\SubscriptionDropReason;

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

echo 'NOTE: If you want to run a category stream you MUST prefix it with `$ce-`!' . PHP_EOL;
echo '--------------------------------------------------------------------------------' . PHP_EOL;

/*******************************************************************************
 * Setting up the event store
 ******************************************************************************/
$eventStore = EventStoreConnectionFactory::create(
	new ConnectionSettings(
		new EndPoint($config['eventstore']['host'], $config['eventstore']['port']),
		EndpointExtensions::HTTP_SCHEMA,
		new UserCredentials($config['eventstore']['user'], $config['eventstore']['pass'])
	)
);

$subscription = $eventStore->subscribeToStreamFrom(
	$stream,
	$checkpoint,
	null,

	new class implements EventAppearedOnCatchupSubscription {
		public function __invoke(
			EventStoreCatchUpSubscription $subscription,
			ResolvedEvent $resolvedEvent
		): void {
			//var_dump($resolvedEvent);
			echo 'Type:     ' . $resolvedEvent->event()->eventType() . PHP_EOL;
			echo 'Number:   ' . $resolvedEvent->event()->eventNumber() . PHP_EOL;
			echo 'Payload:  ' . $resolvedEvent->event()->data() . PHP_EOL;
			echo 'Metadata: ' . $resolvedEvent->event()->metadata() . PHP_EOL;
			echo '--------------------------------------------------------------------------------' . PHP_EOL;
			echo PHP_EOL;
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
			?Throwable $exception = null
		): void {
			echo $reason . PHP_EOL;
		}
	}
);

$subscription->start();
