<?php

namespace SwithFr\Tests\DemoRelations;

use SwithFr\SqlQuery\Contracts\RelationshipInterface;
use SwithFr\Tests\DemoEntities\ProductDemo;

class UserHaveManyProducts implements RelationshipInterface
{
    public function getQueryAggregatedKey(): string
    {
        return "_user_products";
    }

    public function getRelatedClassName(): ?string
    {
        return ProductDemo::class;
    }

    public function hasMany(): bool
    {
        return true;
    }
}