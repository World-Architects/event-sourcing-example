<?php
require 'vendor/autoload.php';
require 'config/config.php';

use App\Domain\Accounting\Account;
use App\Infrastructure\Repository\AccountRepository;
use Prooph\EventStoreHttpClient\EventStoreConnectionFactory;
use Prooph\EventStoreHttpClient\ConnectionSettings;
use Prooph\EventStore\EndPoint;
use Prooph\EventStore\UserCredentials;
use Prooph\EventStore\Transport\Http\EndpointExtensions;
use Psa\EventSourcing\EventStoreIntegration\AggregateReflectionTranslator;
use Psa\EventSourcing\EventStoreIntegration\EventReflectionTranslator;
use Psa\EventSourcing\SnapshotStore\InMemoryStore;

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

/*******************************************************************************
 * Setting up the repository object
 ******************************************************************************/
$repository = new AccountRepository(
	$eventStore,
	new AggregateReflectionTranslator(),
	new EventReflectionTranslator(),
	new InMemoryStore()
);

/*******************************************************************************
 * Create, modify and save the aggregate (with two events)
 ******************************************************************************/
echo '#1 Creating aggregate...' . PHP_EOL;
$account = Account::create(
	'Test',
	'Test'
);

$account->update(
	'Updated name',
	'Updated description'
);
echo 'Aggregate created' . PHP_EOL;
echo PHP_EOL;

echo '#2 Saving aggregate...' . PHP_EOL;
$repository->saveAggregate($account);
echo 'Aggregated saved' . PHP_EOL;
echo PHP_EOL;

/*******************************************************************************
 * Get the aggregate from the store
 ******************************************************************************/
$aggregateId = (string)$account->aggregateId();
$account = $repository->getAggregate($aggregateId);

/*******************************************************************************
 * Create a snapshot of the aggregate
 ******************************************************************************/
echo '#3 Taking Snapshot...' . PHP_EOL;
$repository->createSnapshot($account);
echo 'Snapshot taken' . PHP_EOL;
echo PHP_EOL;

/*******************************************************************************
 * Add another event and save
 ******************************************************************************/
echo '#4 Changing aggregate and saving again...' . PHP_EOL;
$account->update(
	'After Snapshot',
	'After Snapshot'
);
$repository->saveAggregate($account);
echo 'Aggregate saved' . PHP_EOL;
echo PHP_EOL;

/*******************************************************************************
 * Get it again so it will fetch the snapshot first and add the new events to it
 ******************************************************************************/
echo '#5 Reading aggregate again...' . PHP_EOL;
$aggregateId = (string)$account->aggregateId();
$aggregate = $repository->getAggregate($aggregateId);

echo 'Read aggregate ' . $aggregate->aggregateId() . PHP_EOL;
echo 'Dumping aggregate object :' . PHP_EOL;
echo PHP_EOL;

echo var_export($aggregate, true);
