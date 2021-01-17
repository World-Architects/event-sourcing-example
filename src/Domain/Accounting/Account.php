<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace App\Domain\Accounting;

use App\Domain\Accounting\Event\AccountCreated;
use App\Domain\Accounting\Event\AccountUpdated;
use App\Domain\Accounting\Event\CreditAdded;
use App\Domain\Accounting\Event\DebitAdded;
use Psa\EventSourcing\Aggregate\AggregateTrait;
use JsonSerializable;

/**
 * Account Aggregate
 */
final class Account implements JsonSerializable
{
	const AGGREGATE_TYPE = ['Account' => Account::class];

	use AggregateTrait;

	/**
	 * @var string
	 */
	protected string $name;

	/**
	 * @var null|string
	 */
	protected ?string $description;

	/**
	 * @var float
	 */
	protected $balance;

	/**
	 * Disable the constructor, use the create method
	 */
	private function __construct() {
	}

	/**
	 * Create
	 *
	 * @param string $name Name
	 * @param string $description Description
	 * @param float $balance
	 * @return self;
	 * @throws \Exception
	 */
	public static function create(
		string $name,
		string $description,
		float $balance = 0.00
	) {
		$account = new static();
		$account->aggregateId = AccountId::generate();

		$account->recordThat(AccountCreated::create(
			AccountId::fromString($account->aggregateId()),
			$name,
			$description,
			$balance
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
	 * @param float $credit
	 */
	public function addCredit(float $credit)
	{
		$this->recordThat(CreditAdded::create(
			AccountId::fromString((string)$this->aggregateId),
			$credit
		));
	}

	/**
	 * @param float $debit
	 */
	public function addDebit(float $debit)
	{
		$this->recordThat(DebitAdded::create(
			AccountId::fromString((string)$this->aggregateId),
			$debit,
		));
	}

	/**
	 * @param \App\Domain\Accounting\Event\AccountCreated $event Event
	 * @return void
	 */
	public function whenAccountCreated(AccountCreated $event): void
	{
		$this->aggregateId = $event->aggregateId();
		$this->name = $event->name();
		$this->description = $event->description();
	}

	/**
	 * @param \App\Domain\Accounting\Event\AccountUpdated $event Event
	 * @return void
	 */
	public function whenAccountUpdated(AccountUpdated $event): void
	{
		$this->name = $event->name();
		$this->description = $event->description();
	}

	/**
	 * @param \App\Domain\Accounting\Event\CreditAdded $event
	 */
	public function whenCreditAdded(CreditAdded $event)
	{
		$this->balance += $event->amount();
	}

	/**
	 * @param \App\Domain\Accounting\Event\DebitAdded $event
	 */
	public function whenDebitAdded(DebitAdded $event)
	{
		$this->balance -= $event->amount();
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
			'balance' => $this->balance
		];
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize()
	{
		return $this->toArray();
	}
}
