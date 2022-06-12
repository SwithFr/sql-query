<?php

namespace SwithFr\SqlQuery\Contracts;

use PDO;

interface DBInterface
{
    public function pdo(): PDO;
}