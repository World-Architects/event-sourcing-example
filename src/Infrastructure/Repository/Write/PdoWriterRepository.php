<?php

/**
 * PSA Event Sourcing Library
 * Copyright PSA Ltd. All rights reserved.
 */

declare(strict_types=1);

namespace App\Infrastructure\Repository\Write;

use PDO;
use PDOStatement;
use PDOException;

/**
 * Account Repository
 */
class PdoWriterRepository
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
     * @param string $table
     * @param array $data
     * @return void
     */
    public function delete(string $table, $data)
    {
        $query = $this->createDeleteQuery($table, $data);

        $statement = $this->pdo->prepare($query);

        if ($statement === false) {
            $errorInfo = $this->pdo->errorInfo();
            throw new PDOException($errorInfo[2]);
        }

        $statement->execute($data);
    }

    public function query(string $query, $data) {
        $statement = $this->pdo->prepare($query);

        if ($statement === false) {
            $errorInfo = $this->pdo->errorInfo();
            throw new PDOException($errorInfo[2]);
        }

        $statement->execute($data);
    }

    /**
     * @param string $table
     * @param array $data
     * @return string
     */
    protected function createDeleteQuery(string $table, array $data): string
    {
        $query = 'DELETE FROM ' . $table . ' WHERE ';

        $pieces = [];
        foreach ($data as $key => $value) {
            $pieces[] = $key . ' = :' . $key;
        }

        return $query . implode(',', $pieces);
    }

    public function insert(string $table, array $data)
    {
        $queryString = $this->createUpsertQuery($table, $data);
        $statement = $this->pdo->prepare($queryString);

        if ($statement === false) {
            $errorInfo = $this->pdo->errorInfo();
            throw new PDOException($errorInfo[2]);
        }

        $statement->execute($data);
    }

    protected function handlePdoError(PDOStatement $statement)
    {
        if ($statement->errorCode() === PDO::ERR_NONE) {
            return;
        }

        throw new PDOException('Query failed', $statement->errorCode());
    }

    /**
     * @param string $table
     * @param array $data
     * @return string
     */
    protected function createUpsertQuery(string $table, array $data): string
    {
        $insertInto = 'INSERT INTO ' . $table . ' (' .  implode(',', array_keys($data)) . ')' . PHP_EOL;

        $placeholder = implode(', :', array_keys($data));
        $placeholder = 'VALUES (:' . $placeholder . ')' . PHP_EOL;

        $onDuplicateKeyUpdate = 'ON DUPLICATE KEY UPDATE'. PHP_EOL;

        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = $key . ' = VALUES(' . $key . ')';
        }
        $fields = implode(', ', $fields) . PHP_EOL;

        return $insertInto . $placeholder . $onDuplicateKeyUpdate. $fields;
    }
}
