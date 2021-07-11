<?php
require 'vendor/autoload.php';
require 'config/config.php';

use App\Infrastructure\EventStore\EventProcessorCollection;
use App\Infrastructure\EventStore\EventProcessorCollectionFactory;
use App\Infrastructure\EventStore\EventStoreConnectionFactory;
use App\Infrastructure\Database\PdoFactory;
use App\Infrastructure\Repository\Write\PdoWriterRepository;
use Assert\Assert;
use Prooph\EventStore\EventStoreCatchUpSubscription;
use Prooph\EventStore\SubscriptionDropReason;
use Prooph\EventStore\CatchUpSubscriptionSettings;
use Prooph\EventStore\ResolvedEvent;

/*******************************************************************************
 * Shell argument parsing
 ******************************************************************************/

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
 * Setting up the event processors
 ******************************************************************************/
$factory = new EventProcessorCollectionFactory(
    new EventProcessorCollection(),
    new PdoWriterRepository(PdoFactory::create($config['pdo-mariadb']))
);
$collection = $factory->build();

/*******************************************************************************
 * Setting up the event store
 ******************************************************************************/
$eventStore = (new EventStoreConnectionFactory())
    ->createHttpClient($config['eventstore']);

$subscription = $eventStore->subscribeToStreamFrom(
    $stream,
    $checkpoint,
    CatchUpSubscriptionSettings::default(),

    function (
        EventStoreCatchUpSubscription $subscription,
        ResolvedEvent $resolvedEvent
    ) use ($collection): void {
        if ($resolvedEvent->event() === null) {
            return;
        }

        $eventType = $resolvedEvent->event()->eventType();
        if ($collection->hasProcessorsForEvent($eventType)) {
            $processors = $collection->getProcessorsForEvent($eventType);
            foreach ($processors as $processor) {
                $processor($resolvedEvent, $subscription);
            }
        }
    },

    function (EventStoreCatchUpSubscription $subscription): void {
        echo 'Started live processing on ' . (string)$subscription->streamId() . PHP_EOL;
    },

    function(
        EventStoreCatchUpSubscription $subscription,
        SubscriptionDropReason $reason,
        ?Throwable $exception = null
    ): void {
        echo $reason . PHP_EOL;
    }
);

$subscription->start();
