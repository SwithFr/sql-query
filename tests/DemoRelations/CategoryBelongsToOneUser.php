<?php

namespace SwithFr\Tests\DemoRelations;

use SwithFr\SqlQuery\Contracts\RelationshipInterface;
use SwithFr\Tests\DemoEntities\UserDemo;

class CategoryBelongsToOneUser implements RelationshipInterface
{

    public function getQueryAggregatedKey(): string
    {
        return "_category_user";
    }

    public function getRelatedClassName(): ?string
    {
        return UserDemo::class;
    }

    public function hasMany(): bool
    {
        return false;
    }
}