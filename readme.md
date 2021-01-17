# Event Sourcing Example App

## Prerequisite

A basic understanding of event sourcing and domain modelling.
 
## Goals

 * Demonstrate event sourcing
 * Demonstrate minimal / none dependencies on the domain for the event sourcing
 * Demonstrate projections

## Event Store Settings

All the code uses the Event Store default credentials:

 * **username**: admin
 * **password**: changeit
 * **HTTP port**: 2113
 * **TCP/IP**: 1113

You can change the configuration in [config/config.php](../config/config.php).

If you need further assistance with the event store please check the official documentation https://eventstore.org/docs/.

## Shell Scripts

### HTTP Client

Running AggregateTest.php will generate a new stream with two events and output the aggregate id and data if it was successful.

```sh
php .\AggregateTest.php'
```

You need to pass the stream, for the example app this is `Account-<aggregate-uuid>`.

If you use the `--checkpoint` option, you can reply events from a given position in the stream. If not it will start with the first event.

```sh
php .\CatchupSubscriptionTest.php --stream='<stream-name>'
```

Our example domain is `Account` and the according stream is:

```sh
php .\CatchupSubscriptionTest.php --stream=$ce-Account'
```

### Async Client

Basically the same as above just that these files implement the async client library.

```sh
php .\AsyncAggregateTest.php
```

```sh
php .\AsyncCatchupSubscription.php --stream='<stream-name>'
```
