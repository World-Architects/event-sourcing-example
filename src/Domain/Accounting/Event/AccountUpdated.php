<?php
declare(strict_types=1);

namespace App\Domain\Accounting\Event;

use App\Domain\Accounting\AccountId;

/**
 * Account Created Event
 */
class AccountUpdated
{
	const EVENT_TYPE = 'Accounting.Account.updated';

	protected $accountId;
	protected $name;
	protected $description;

	/**
	 *
	 */
	public static function create(
		AccountId $accountId,
		string $name,
		string $description
	) {
		$event = new self();
		$event->aggregateId = (string)$accountId;
		$event->name = $name;
		$event->description = $description;

		return $event;
	}

	public function aggregateId(): AccountId
	{
		return AccountId::fromString($this->aggregateId);
	}

	public function name(): string
	{
		return $this->name;
	}

	public function description(): string
	{
		return $this->description;
	}
}
