<?php

namespace SwithFr\Tests\DemoRelations;

use stdClass;
use SwithFr\SqlQuery\Contracts\RelationshipInterface;
use SwithFr\Tests\DemoEntities\CategoryDemo;

class ProductHaveOneCategory implements RelationshipInterface
{
    public function __construct(private string $name = 'category', private string $queryAggregatedKey = '_category')
    {
    }

    public function getQueryAggregatedKey(): string
    {
        return $this->queryAggregatedKey;
    }

    public function getRelatedClassName(): ?string
    {
        return $this->name === 'category' ? CategoryDemo::class : stdClass::class;
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