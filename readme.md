# Event Sourcing Example App

## Goals:

 * Demonstrate event sourcing
 * Demonstrate minimal / none dependencies on the domain for event sourcing
 * Demonstrate projections

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

### Async Client

Basically the same as above just that these files implement the async client library.

```sh
php AsyncAggregateTest.php
```

```sh
php AsyncCatchupSubscription.php --stream $ce-Account
```
