<?php
require 'vendor/autoload.php';
require 'config/config.php';

use App\Domain\Accounting\Account;
use App\Infrastructure\EventStore\EventStoreConnectionFactory;
use App\Infrastructure\Repository\AsyncAccountRepository;
use Amp\Loop;
use Psa\EventSourcing\EventStoreIntegration\AggregateReflectionTranslator;
use Psa\EventSourcing\EventStoreIntegration\EventReflectionTranslator;
use Psa\EventSourcing\SnapshotStore\InMemoryStore;

Loop::run(function() use ($config) {
/*******************************************************************************
 * Setting up the event store and connect
 ******************************************************************************/
    $eventStore = (new EventStoreConnectionFactory())
        ->createAsynClient($config['eventstore-async']);

    $eventStore->onConnected(function (): void {
        echo 'Connected' . PHP_EOL;
    });

    $eventStore->onClosed(function (): void {
        echo 'Connection closed' . PHP_EOL;
    });

    $eventStore->connectAsync();

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
 * Restore the aggregate and save again with some added events
 ******************************************************************************/
    $aggregateId = (string)$account->aggregateId();
    $account = $repository->getAggregate($aggregateId);

    $account->update(
        'After Snapshot Name',
        'After Snapshot Description'
    );

    $account->addCredit(50.00);
    $account->addDebit(25.00);
    $account->addCredit(50.00);

    $repository->saveAggregate($account);

    $result = $repository->getAggregate($aggregateId);
    var_dump($result);

    Loop::stop();
});
