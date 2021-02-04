<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace App\Infrastructure\Repository\Read;

use PDO;

/**
 * Account Repository
 */
class AccountReadRepository
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @param \PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param string $uuid UUID as string
     * @return array
     */
    public function getAccountById(string $uuid)
    {
        $query = 'SELECT * FROM accounts where id = :uuid';

        $statement = $this->pdo->prepare($query);
        $statement->execute([
            'uuid' => $uuid
        ]);

        return $statement->fetch();
    }
}
