<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace App\Domain;

use JsonSerializable;

/**
 * Account Aggregate
 */
interface AggregateInterface extends JsonSerializable
{
    /**
     * @return array
     */
    public function toArray(): array;
}
