<?php

namespace SwithFr\Tests\DemoRelations;

use SwithFr\SqlQuery\Relationship;
use SwithFr\SqlQuery\Contracts\RelationshipInterface;
use SwithFr\Tests\DemoEntities\ProductDemo;

class UserHaveManyProducts extends Relationship
{
    public function __construct(
        protected string $name = 'user.products',
        protected string $queryAggregatedKey = '_user_products'
    ) {
    }

    public function getRelatedClassName(): ?string
    {
        return ProductDemo::class;
    }

    public function hasMany(): bool
    {
        return true;
    }

    public function getJoinQuery(): string
    {
        return 'left join products on products.user_id = users.id';
    }

    public function getRelatedTable(): string
    {
        return 'users';
    }
}