<?php
$ds = DIRECTORY_SEPARATOR;

require 'vendor' . $ds . 'autoload.php';
require 'config' . $ds . 'config.php';

use App\Domain\Accounting\Account;
use App\Infrastructure\EventStore\EventStoreConnectionFactory;
use App\Infrastructure\Database\PdoFactory;
use App\Infrastructure\Repository\AccountRepository;
use Psa\EventSourcing\EventStoreIntegration\AggregateReflectionTranslator;
use Psa\EventSourcing\EventStoreIntegration\EventReflectionTranslator;
use Psa\EventSourcing\SnapshotStore\PdoSqlStore;

/*******************************************************************************
 * Setting up the event store
 ******************************************************************************/
$eventStore = (new EventStoreConnectionFactory())
    ->createHttpClient($config['eventstore']);

/*******************************************************************************
 * Setting up the repository objects
 ******************************************************************************/
$repository = new AccountRepository(
    $eventStore,
    new AggregateReflectionTranslator(),
    new EventReflectionTranslator(),
    new PdoSqlStore(PdoFactory::create($config['pdo-mariadb']))
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
    'After Snapshot Name',
    'After Snapshot Description'
);

$account->addCredit(50.00);
$account->addDebit(25.00);
$account->addCredit(50.00);

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
echo 'Dumping aggregate object:' . PHP_EOL;
echo PHP_EOL;

echo var_export($aggregate, true);
