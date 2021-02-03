<?php
declare(strict_types=1);

namespace App\Infrastructure\EventStore;

use Prooph\EventStore\Async\EventStoreConnection as AsyncEventStoreConnection;
use Prooph\EventStore\EventStoreConnection;
use Prooph\EventStoreHttpClient\EventStoreConnectionFactory as ProophEventStoreConnectionFactory;
use Prooph\EventStoreClient\EventStoreConnectionFactory as ProophAsyncEventStoreConnectionFactory;
use Prooph\EventStoreHttpClient\ConnectionSettings;
use Prooph\EventStore\EndPoint;
use Prooph\EventStore\UserCredentials;
use Prooph\EventStore\Transport\Http\EndpointExtensions;
use Psr\Log\NullLogger;

/**
 * Class EventStoreConnectionFactory
 *
 * @package App\Infrastructure\EventStore
 */
class EventStoreConnectionFactory
{
    /**
     * @param array $config
     * @return \Prooph\EventStore\EventStoreConnection
     */
    public function createHttpClient(array $config): EventStoreConnection
    {
        return ProophEventStoreConnectionFactory::create(
            new ConnectionSettings(
                new NullLogger(),
                true,
                new EndPoint($config['host'], $config['port']),
                EndpointExtensions::HTTP_SCHEMA,
                new UserCredentials($config['user'], $config['pass'])
            )
        );
    }

    /**
     * @param array $config
     * @return \Prooph\EventStore\Async\EventStoreConnection
     */
    public function createAsynClient(array $config): AsyncEventStoreConnection
    {
        return ProophAsyncEventStoreConnectionFactory::createFromEndPoint(
            new EndPoint(
                $config['host'],
                $config['port']
            )
        );
    }
}
