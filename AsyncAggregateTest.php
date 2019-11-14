<?php
require 'vendor/autoload.php';

use App\Domain\Accounting\Account;
use App\Infrastructure\Repository\AsyncAccountRepository;
use Amp\Loop;
use Prooph\EventStoreClient\EventStoreConnectionFactory;
use Prooph\EventStore\EndPoint;
use Psa\EventSourcing\EventStoreIntegration\AggregateReflectionTranslator;
use Psa\EventSourcing\EventStoreIntegration\EventReflectionTranslator;

Amp\Loop::run(function() {
	/*******************************************************************************
	 * Setting up the event store
	 ******************************************************************************/
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

	/*******************************************************************************
	 * Setting up the repository object
	 ******************************************************************************/
	$repository = new AsyncAccountRepository(
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

	/**
	 * @var $result \Prooph\EventStore\WriteResult
	 */
	$repository->saveAggregate($account);
	/*******************************************************************************
	 * Restore the aggregate
	 ******************************************************************************/
	$aggregateId = (string)$account->aggregateId();
	$aggregate = $repository->getAggregate($aggregateId);
	var_dump($aggregate);

	Loop::stop();
});
