<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Accounting\Account;
use Psa\EventSourcing\Aggregate\AbstractAggregateRepository;

/**
 * Account Repository
 */
class AccountRepository extends AbstractAggregateRepository
{
	const AGGREGATE_TYPE = ['Account' => Account::class];
}