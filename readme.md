# Event Sourcing Example App

## Prerequisite

A basic understanding of [event sourcing](https://martinfowler.com/eaaDev/EventSourcing.html) and [domain modelling](https://en.wikipedia.org/wiki/Domain_model).
 
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
 * **Host**: 127.0.0.1

You can change the configuration in [config/config.php](../config/config.php).

If you need further assistance with the event store please check the official documentation https://eventstore.org/docs/.

## Running the examples

### Create the SQL database

The SQL database it is just used to write the data processed in the subscriptions, to demonstrate this, but it is not required for the event store or event sourcing itself.

Create a database called `accounting` and run the two SQL files in the [resources](./resources) folder.

Configure your SQL connection in [config/config.php](../config/config.php).

### HTTP Event Store Client

Running AggregateExample.php will generate a new stream with two events and output the aggregate id and data if it was successful.

```sh
php .\AggregateExample.php'
```

You need to pass the stream, for the example app this is `Account-<aggregate-uuid>`.

If you use the `--checkpoint` option, you can reply events from a given position in the stream. If not it will start with the first event.

```sh
php .\CatchupSubscriptionExample.php --stream='<stream-name>'
```

Starting at version 4

```sh
php .\CatchupSubscriptionExample.php --stream='<stream-name>' --checkpoint 4
```

To run a subscription on a **category** stream prefix it we `$ce-` followed by the name of it:

```sh
php .\CatchupSubscriptionExample.php --stream=$ce-Account'
```

Please note that the checkpoint is not applicable here.

### Async Event Store Client

Basically the same as above just that these files implement the async client library.

```sh
php .\AsyncAggregateExample.php
```

```sh
php .\AsyncCatchupSubscriptionExample.php --stream='<stream-name>'
```
