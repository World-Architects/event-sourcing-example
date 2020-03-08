<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Accounting\Account;
use Psa\EventSourcing\Aggregate\AsyncAggregateRepository;

/**
 * Account Repository
 */
class AsyncAccountRepository extends AsyncAggregateRepository
{
	const AGGREGATE_TYPE = Account::AGGREGATE_TYPE;
}
