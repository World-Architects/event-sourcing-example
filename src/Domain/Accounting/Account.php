<?php
declare(strict_types=1);

namespace App\Domain\Accounting;

use App\Domain\Accounting\Event\AccountCreated;
use App\Domain\Accounting\Event\AccountUpdated;
use Iterator;

/**
 * Account Aggregate
 */
class Account
{
	const AGGREGATE_TYPE = 'Account';

	/**
	 * @var \App\Accounting\AccountId
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var null|string
	 */
	protected $description;

	/**
	 * @var array
	 */
	protected $events = [];

	/**
	 * @var int
	 */
	protected $aggregateVersion = 0;

	/*
	 * @var string
	 */
	protected $aggregateId;

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
		$account = new self();
		$account->id = AccountId::generate();
		$account->aggregateId = (string)$account->id;

		$account->recordThat(AccountCreated::create(
			$account->id,
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
			AccountId::fromString((string)$this->id),
			$name,
			$description
		));

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function aggregateId(): string
	{
		return (string)$this->id;
	}

	/**
	 * @param \App\Domain\Accounting\Model\Event\AccountCreated $event Event
	 */
	public function whenAccountCreated(AccountCreated $event): void
	{
		$this->id = $event->accountId();
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
			'accountId' => (string)$this->id,
			'name' => $this->name,
			'description' => $this->description,
		];
	}

	/**
	 * @param object $event Event
	 * @return void
	 */
	public function recordThat(object $event): void
	{
		$this->events[] = $event;
	}

	/**
	 * @param \Iterator $events Events
	 * @return self
	 */
	public static function reconstituteFromHistory(Iterator $events)
	{
		$self = new self();
		foreach ($events as $event) {
			$self->applay($event);
			$self->aggregateVersion++;
		}

		return $self;
	}

	/**
	 * @param object $event Event Object
	 * @return void
	 */
	public function applay(object $event)
	{
		$classParts = explode('\\', get_class($event));
		$method = 'when' . end($classParts);
		if (method_exists($this, $method)) {
			$this->{$method}($event);
		}
	}
}
