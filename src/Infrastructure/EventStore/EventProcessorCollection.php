<?php

declare(strict_types=1);

namespace App\Infrastructure\EventStore;

/**
 * Class EventStoreConnectionFactory
 *
 * @package App\Infrastructure\EventStore
 */
class EventProcessorCollection
{
    /**
     * @var array<array>
     */
    protected array $events = [];

    /**
     * @param string $event
     * @param callable $callable
     * @return void
     */
    public function add(string $event, callable $callable): void
    {
        if (!isset($this->events[$event])) {
            $this->events[$event] = [];
        }

        $this->events[$event][] = $callable;
    }

    /**
     * @param string $event
     * @return bool
     */
    public function hasProcessorsForEvent(string $event): bool
    {
        return !empty($this->events[$event]);
    }

    /**
     * @param string $event
     * @return callable[]
     */
    public function getProcessorsForEvent(string $event): array
    {
        if (!$this->hasProcessorsForEvent($event)) {
            return [];
        }

        return $this->events[$event];
    }
}
