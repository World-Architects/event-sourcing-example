<?php
require 'vendor/autoload.php';

use Prooph\EventStoreClient\EventStoreConnectionFactory;
use Prooph\EventStore\EndPoint;

$eventStore = EventStoreConnectionFactory::createFromEndPoint(
	new EndPoint('127.0.0.1', 1113)
);

$eventStore->onConnected(function (): void {
	echo 'Connected' . PHP_EOL;
});

$eventStore->onClosed(function (): void {
	echo 'Connection closed' . PHP_EOL;
});

$eventStore->connectAsync();
