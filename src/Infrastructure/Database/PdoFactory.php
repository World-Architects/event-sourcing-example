<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace App\Infrastructure\Database;

use PDO;

/**
 * PDO Factory
 */
class PdoFactory
{
    /**
     * @param array<mixed> $config
     * @return \PDO
     */
    public static function create(array $config): PDO
    {
        return new PDO(
            $config['dsn'],
            $config['user'],
            $config['pass']
        );
    }
}
