<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace App\Domain\Accounting\Event;

use App\Domain\Accounting\AccountId;

/**
 * DebitAdded
 */
final class DebitAdded
{
    public const EVENT_TYPE = 'Accounting.Account.debitAdded';

    /**
     * @var float
     */
    protected float $amount = 0.0;

    /**
     * @var string
     */
    protected string $aggregateId;

    /**
     * @param  AccountId $accountId Account Id
     * @param  float     $amount    Amount
     * @return self
     */
    public static function create(
        AccountId $accountId,
        float $amount
    ) {
        $event = new self();
        $event->aggregateId = (string)$accountId;
        $event->amount = $amount;

        return $event;
    }

    public function amount(): float
    {
        return $this->amount;
    }
}
