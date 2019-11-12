<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Accounting\Account;
use Psa\EventSourcing\Aggregate\AbtractAsyncAggregateRepository;

/**
 * Account Repository
 */
class AccountRepository extends AbtractAsyncAggregateRepository
{
	const AGGREGATE_TYPE = ['Account' => Account::class];
}
