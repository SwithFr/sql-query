<?php

namespace SwithFr\SqlQuery;

use PDO;
use PDOException;
use PDOStatement;
use SwithFr\SqlQuery\Contracts\DBInterface;

class PgsqlDatabase implements DBInterface
{
    private PDO $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new PDO("pgsql:host=postgres;port=5432;dbname=root;user=root;password=root");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            dd($e);
        }
    }

    public function queryStringToStatement(string $query): PDOStatement
    {
        try {
            return $this->pdo->prepare($query);
        } catch (PDOException $e) {
            dd($e->getMessage());
        }
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}