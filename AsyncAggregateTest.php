<?php
require 'vendor/autoload.php';
require 'config/config.php';

use App\Domain\Accounting\Account;
use App\Infrastructure\Repository\AsyncAccountRepository;
use Amp\Loop;
use Prooph\EventStoreClient\EventStoreConnectionFactory;
use Prooph\EventStore\EndPoint;
use Psa\EventSourcing\EventStoreIntegration\AggregateReflectionTranslator;
use Psa\EventSourcing\EventStoreIntegration\EventReflectionTranslator;
use Psa\EventSourcing\SnapshotStore\InMemoryStore;

Amp\Loop::run(function() use ($config) {
	/*******************************************************************************
	 * Setting up the event store
	 ******************************************************************************/
	$eventStore = EventStoreConnectionFactory::createFromEndPoint(
		new EndPoint($config['eventstore']['host'], $config['eventstore']['port'])
	);

	$eventStore->onConnected(function (): void {
		echo 'Connected' . PHP_EOL;
	});

	$eventStore->onClosed(function (): void {
		echo 'Connection closed' . PHP_EOL;
	});

	yield $eventStore->connectAsync();

	/*******************************************************************************
	 * Setting up the repository object
	 ******************************************************************************/
	$repository = new AsyncAccountRepository(
		$eventStore,
		new AggregateReflectionTranslator(),
		new EventReflectionTranslator(),
		new InMemoryStore()
	);

	/*******************************************************************************
	 * Create, modify and save the aggregate (with two events)
	 ******************************************************************************/
	$account = Account::create(
		'Test',
		'Test'
	);

	/**
	 * @var $result \Prooph\EventStore\WriteResult
	 */
	$repository->saveAggregate($account);
	/*******************************************************************************
	 * Restore the aggregate
	 ******************************************************************************/
	$aggregateId = (string)$account->aggregateId();
	$account = $repository->getAggregate($aggregateId);

	for ($i = 1; $i <= 5; $i++) {
		$account->update(
			'Updated ' . $i,
			'Updated ' . $i
		);
	}

	$repository->saveAggregate($account);
	$repository->createSnapshot($account);

	for ($i = 5; $i <= 10; $i++) {
		$account->update(
			'Updated ' . $i,
			'Updated ' . $i
		);
	}

	$repository->saveAggregate($account);

	$result = $repository->getAggregate($aggregateId);
	var_dump($result);

	Loop::stop();
});
