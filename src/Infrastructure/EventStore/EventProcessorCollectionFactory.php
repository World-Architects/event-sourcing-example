<?php

declare(strict_types=1);

namespace App\Infrastructure\EventStore;

use App\Domain\Accounting\Event\AccountCreated;
use App\Domain\Accounting\Event\CreditAdded;
use App\Domain\Accounting\Event\DebitAdded;
use App\Infrastructure\Repository\Write\PdoWriterRepository;
use Prooph\EventStore\ResolvedEvent;

/**
 * Class EventStoreConnectionFactory
 *
 * @package App\Infrastructure\EventStore
 */
class EventProcessorCollectionFactory
{
    /**
     * @var \App\Infrastructure\Repository\Write\PdoWriterRepository
     */
    protected PdoWriterRepository $pdoWriterRepository;

    /**
     * @var \App\Infrastructure\EventStore\EventProcessorCollection
     */
    protected EventProcessorCollection $collection;

    public function __construct(
        EventProcessorCollection $eventProcessorCollection,
        PdoWriterRepository $pdoWriterRepository
    ) {
        $this->collection = $eventProcessorCollection;
        $this->pdoWriterRepository = $pdoWriterRepository;
    }

    /**
     * @return \App\Infrastructure\EventStore\EventProcessorCollection
     */
    public function build(): EventProcessorCollection
    {
        $this->addConsoleOutputProcessorToEvents([
            AccountCreated::class,
            CreditAdded::class,
            DebitAdded::class
        ]);

        $this->addAccountCreated();
        $this->addCreditAdded();
        $this->addDeditAdded();

        return $this->collection;
    }

    protected function getDataFromEvent(ResolvedEvent $resolvedEvent): array
    {
        if ($resolvedEvent->event()->data() === null) {
            return [];
        }

        return json_decode($resolvedEvent->event()->data(), true, 512, JSON_THROW_ON_ERROR);
    }

    protected function addAccountCreated()
    {
        $this->collection->add(AccountCreated::class, function (ResolvedEvent $resolvedEvent) {
            $data = $this->getDataFromEvent($resolvedEvent);

            $this->pdoWriterRepository->insert('accounts', [
                    'id' => $data['aggregateId'],
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'balance' => $data['balance'],
                ]);
        });
    }

    protected function addCreditAdded(): void
    {
        $this->collection->add(CreditAdded::class, function (ResolvedEvent $resolvedEvent) {
            $data = $this->getDataFromEvent($resolvedEvent);

            $this->pdoWriterRepository->query('UPDATE accounts SET balance = balance + :amount WHERE id = :id', [
                'id' => $data['aggregateId'],
                'amount' => $data['amount']
            ]);
        });
    }

    protected function addDeditAdded(): void
    {
        $this->collection->add(DebitAdded::class, function (ResolvedEvent $resolvedEvent) {
            $data = $this->getDataFromEvent($resolvedEvent);

            $this->pdoWriterRepository->query('UPDATE accounts SET balance = balance - :amount WHERE id = :id', [
                'id' => $data['aggregateId'],
                'amount' => $data['amount']
            ]);
        });
    }

    /**
     * @param array $events
     */
    protected function addConsoleOutputProcessorToEvents(array $events): void
    {
        $callable = function (ResolvedEvent $resolvedEvent) {
            if ($resolvedEvent->event() === null) {
                return;
            }

            echo 'Type:     ' . $resolvedEvent->event()->eventType() . PHP_EOL;
            echo 'Number:   ' . $resolvedEvent->event()->eventNumber() . PHP_EOL;
            echo 'Payload:  ' . $resolvedEvent->event()->data() . PHP_EOL;
            echo 'Metadata: ' . $resolvedEvent->event()->metadata() . PHP_EOL;
            echo '--------------------------------------------------------------------------------' . PHP_EOL;
            echo PHP_EOL;
        };

        foreach ($events as $event) {
            $this->collection->add($event, $callable);
        }
    }
}
