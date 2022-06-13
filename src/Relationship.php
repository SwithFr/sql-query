<?php

namespace SwithFr\SqlQuery;

use SwithFr\SqlQuery\Contracts\RelationshipInterface;

abstract class Relationship implements RelationshipInterface
{
    protected string $name;

    protected string $queryAggregatedKey;

    public function getName(): string
    {
        return $this->name;
    }

    public function getQueryAggregatedKey(): string
    {
        return $this->queryAggregatedKey;
    }

    public function hasMany(): bool
    {
        return false;
    }
}