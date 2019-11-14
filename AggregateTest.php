<?php
require 'vendor/autoload.php';

use App\Domain\Accounting\Account;
use App\Infrastructure\Repository\AccountRepository;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client;
use Prooph\EventStoreHttpClient\EventStoreConnectionFactory;
use Prooph\EventStoreHttpClient\ConnectionSettings;
use Prooph\EventStore\EndPoint;
use Prooph\EventStore\UserCredentials;
use Prooph\EventStore\Transport\Http\EndpointExtensions;
use Psa\EventSourcing\EventStoreIntegration\AggregateReflectionTranslator;
use Psa\EventSourcing\EventStoreIntegration\EventReflectionTranslator;

/*******************************************************************************
 * Setting up the event store
 ******************************************************************************/
$eventStore = EventStoreConnectionFactory::create(
	new ConnectionSettings(
		new EndPoint('127.0.0.1', 8012),
		!empty($config['schema']) ?: \Prooph\EventStore\Transport\Http\EndpointExtensions::HTTP_SCHEMA,
		new \Prooph\EventStore\UserCredentials('admin', 'changeit')
	)
);

/*******************************************************************************
 * Setting up the repository object
 ******************************************************************************/
$repository = new AccountRepository(
	$eventStore,
	new AggregateReflectionTranslator(),
	new EventReflectionTranslator()
);

/*******************************************************************************
 * Create, modify and save the aggregate (with two events)
 ******************************************************************************/
$account = Account::create(
	'Test',
	'Test'
);

$account->update(
	'Updated name',
	'Updated description'
);

$repository->saveAggregate($account);

/*******************************************************************************
 * Restore the aggregate
 ******************************************************************************/

$aggregateId = (string)$account->aggregateId();
$aggregate = $repository->getAggregate($aggregateId);

echo 'Read aggregate ' . $aggregate->aggregateId() . PHP_EOL;

var_dump($aggregate);
