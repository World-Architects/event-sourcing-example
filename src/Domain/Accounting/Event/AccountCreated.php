<?php
declare(strict_types=1);

namespace App\Domain\Accounting\Event;

use App\Domain\Accounting\AccountId;

/**
 * Account Created Event
 */
class AccountCreated
{
	const EVENT_TYPE = 'Accounting.Account.created';

	protected $accountId;
	protected $accountNumber;
	protected $currency;
	protected $name;
	protected $description;

	public static function create(
		AccountId $accountId,
		string $name,
		string $description
	) {
		$event = new self();
		$event->accountId = $accountId;
		$event->name = $name;
		$event->description = $description;

		return $event;
	}

	public function accountId(): AccountId
	{
		return $this->accountId;
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
