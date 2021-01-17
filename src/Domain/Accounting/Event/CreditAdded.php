<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace App\Domain\Accounting\Event;

use App\Domain\Accounting\AccountId;

/**
 * CreditAdded
 */
final class CreditAdded
{
	public const EVENT_TYPE = 'Accounting.Account.creditAdded';

	protected float $amount = 0.0;

	protected string $aggregateId;

	/**
	 * @param AccountId $accountId Account Id
	 * @param float $amount Amount
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
