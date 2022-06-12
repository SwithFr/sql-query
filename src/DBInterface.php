<?php

namespace SwithFr\SqlQuery;

use PDO;

interface DBInterface
{
    public function pdo(): PDO;
}