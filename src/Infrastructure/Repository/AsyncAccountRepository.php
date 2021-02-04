<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Accounting\Account;
use Psa\EventSourcing\Aggregate\AsyncAggregateRepository;

/**
 * Account Repository
 */
class AsyncAccountRepository extends AsyncAggregateRepository
{
    public const AGGREGATE_TYPE = ['Account' => Account::class];
}
