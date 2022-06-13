<?php

namespace SwithFr\Tests\DemoRelations;

use SwithFr\SqlQuery\Contracts\RelationshipInterface;
use SwithFr\Tests\DemoEntities\UserDemo;

class CategoryBelongsToOneUser implements RelationshipInterface
{
    public function __construct(private string $name = 'category.user', private string $queryAggregatedKey = '_category_user')
    {
    }

    public function getQueryAggregatedKey(): string
    {
        return $this->queryAggregatedKey;
    }

    public function getRelatedClassName(): ?string
    {
        return UserDemo::class;
    }

    public function hasMany(): bool
    {
        return false;
    }

    public function getName(): string
    {
        return $this->name;
    }
}