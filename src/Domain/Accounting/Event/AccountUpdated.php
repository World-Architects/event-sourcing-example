<?php
declare(strict_types=1);

namespace App\Domain\Accounting\Event;

use App\Domain\Accounting\AccountId;

/**
 * Account Created Event
 */
class AccountUpdated
{
	public const EVENT_TYPE = 'Accounting.Account.updated';

	/**
	 * @var string
	 */
	protected $aggregateId;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @param AccountId $accountId Account Id
	 * @param string $name Name
	 * @param string $description Description
	 * @return self
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

	/**
	 * @return string
	 */
	public function aggregateId(): string
	{
		return $this->aggregateId;
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
}
