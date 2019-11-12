# Event Sourcing Example App

## Goals:

 * Demonstrate event sourcing
 * Demonstrate minimal / none dependencies on the domain for event sourcing
 * Demonstrate projections

## Shell Scripts

Listen permanently to *all* incoming events of the given steam

```sh
php Subscription.php --stream $ce-Account
```

Running this will generate a stream and some events
```sh
php AggregateTest.php
```

This will re-run events

```sh
php CatchupSubscription.php --stream $ce-Account
```
