<?php

namespace SwithFr\Tests;

use PDO;
use PDOException;
use SwithFr\SqlQuery\Contracts\DBInterface;

class PgsqlDB implements DBInterface
{
    public PDO $pdo;

    public function __construct()
    {
        try {
            $host = getenv('POSTGRES_HOST') ?: 'localhost';
            $this->pdo = new PDO("pgsql:host=$host;port=5432;dbname=root;user=root;password=root");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            dd($e);
        }
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }
}