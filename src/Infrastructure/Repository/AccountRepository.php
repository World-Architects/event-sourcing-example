<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Accounting\Account;
use Psa\EventSourcing\Aggregate\AggregateRepository;

/**
 * Account Repository
 */
class AccountRepository extends AggregateRepository
{
	public const AGGREGATE_TYPE = ['Account' => Account::class];
}
