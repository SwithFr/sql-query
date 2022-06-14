<?php

namespace SwithFr\SqlQuery\Contracts;

use PDO;
use PDOStatement;

interface DBInterface
{
    public function queryStringToStatement(string $query): PDOStatement;

    public function getPdo(): PDO;
}