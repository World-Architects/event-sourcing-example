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
    protected array $events = [];

    public function add(string $event, callable $callable)
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

    public function getProcessorsForEvent(string $event): array
    {
        if (!$this->hasProcessorsForEvent($event)) {
            return [];
        }

        return $this->events[$event];
    }
}
