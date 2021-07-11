<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace App\Domain\Accounting\Event;

use App\Domain\Accounting\AccountId;

/**
 * Account Created Event
 */
final class AccountCreated
{
    public const EVENT_TYPE = 'Accounting.Account.created';

    /**
     * @var string
     */
    protected string $aggregateId;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $description;

    /**
     * @var float
     */
    protected float $balance;

    /**
     * @param  AccountId $accountId   Account Id
     * @param  string    $name        Name
     * @param  string    $description Description
     * @param  float     $balance     Balance
     * @return self
     */
    public static function create(
        AccountId $accountId,
        string $name,
        string $description,
        float $balance
    ) {
        $event = new self();
        $event->aggregateId = (string)$accountId;
        $event->name = $name;
        $event->description = $description;
        $event->balance = $balance;

        return $event;
    }

    /**
     * @return string
     */
    public function aggregateId(): string
    {
        return (string)$this->aggregateId;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * @return float
     */
    public function balance()
    {
        return $this->balance;
    }
}
