<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Accounting\Account;
use Psa\EventSourcing\Aggregate\AbstractAsyncAggregateRepository;

/**
 * Account Repository
 */
class AsyncAccountRepository extends AbstractAsyncAggregateRepository
{
	const AGGREGATE_TYPE = ['Account' => Account::class];
}
