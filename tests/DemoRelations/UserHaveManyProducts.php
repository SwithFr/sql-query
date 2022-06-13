<?php

namespace SwithFr\Tests\DemoRelations;

use SwithFr\SqlQuery\Contracts\RelationshipInterface;
use SwithFr\Tests\DemoEntities\ProductDemo;

class UserHaveManyProducts implements RelationshipInterface
{
    public function __construct(private string $name = 'user.products', private string $queryAggregatedKey = '_user_products')
    {
    }

    public function getQueryAggregatedKey(): string
    {
        return $this->queryAggregatedKey;
    }

    public function getRelatedClassName(): ?string
    {
        return ProductDemo::class;
    }

    public function hasMany(): bool
    {
        return true;
    }

    public function getName(): string
    {
        return $this->name;
    }
}