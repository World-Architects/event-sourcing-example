<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace App\Domain\Accounting;

use JsonSerializable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * AccountId
 */
final class AccountId implements JsonSerializable
{
    /**
     * UUID
     *
     * @var \Ramsey\Uuid\UuidInterface
     */
    private $uuid;

    /**
     * Generates a new Id
     *
     * @throws \Exception
     * @return self
     */
    public static function generate(): AccountId
    {
        return new self(Uuid::uuid4());
    }

    /**
     * @param string $userId
     * @return self
     */
    public static function fromString(string $userId): AccountId
    {
        return new self(Uuid::fromString($userId));
    }

    /**
     * Constructor
     *
     * @param \Ramsey\Uuid\UuidInterface $uuid UUID
     */
    private function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function jsonSerialize(): string
    {
        return $this->uuid->toString();
    }

    /**
     * To string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->uuid->toString();
    }
}
