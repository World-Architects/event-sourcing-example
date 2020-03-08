<?php
declare(strict_types=1);

namespace App\Domain\Accounting;

use App\Domain\Accounting\Event\AccountCreated;
use App\Domain\Accounting\Event\AccountUpdated;
use Iterator;
use Psa\EventSourcing\Aggregate\AggregateTrait;

/**
 * Account Aggregate
 */
class Account
{
	const AGGREGATE_TYPE = ['Account' => Account::class];

	use AggregateTrait;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var null|string
	 */
	protected $description;

	private function __construct() {
	}

	/**
	 * Create
	 *
	 * @param string $name Name
	 * @param string $description Description
	 */
	public static function create(
		string $name,
		string $description
	) {
		$account = new static();
		$account->aggregateId = AccountId::generate();

		$account->recordThat(AccountCreated::create(
			AccountId::fromString($account->aggregateId()),
			$name,
			$description
		));

		return $account;
	}

	/**
	 * Updates name and description
	 *
	 * @param string $name Name
	 * @param string $description Description
	 * @return $this
	 */
	public function update(string $name, string $description)
	{
		$this->recordThat(AccountUpdated::create(
			AccountId::fromString((string)$this->aggregateId),
			$name,
			$description
		));

		return $this;
	}

	/**
	 * @param \App\Domain\Accounting\Model\Event\AccountCreated $event Event
	 */
	public function whenAccountCreated(AccountCreated $event): void
	{
		$this->aggregateId = $event->aggregateId();
		$this->name = $event->name();
		$this->description = $event->description();
	}

	/**
	 * @param \App\Domain\Accounting\Model\Event\AccountUpdated $event Event
	 */
	public function whenAccountUpdated(AccountUpdated $event): void
	{
		$this->name = $event->name();
		$this->description = $event->description();
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return [
			'accountId' => (string)$this->aggregateId,
			'name' => $this->name,
			'description' => $this->description,
		];
	}
}
