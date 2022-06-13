<?php

namespace SwithFr\Tests\DemoRelations;

use SwithFr\SqlQuery\Contracts\RelationshipInterface;
use SwithFr\Tests\DemoEntities\TagDemo;

class ProductHaveManyTags implements RelationshipInterface
{
    public function __construct(private string $name = 'tags', private string $queryAggregatedKey = '_tags')
    {
    }

    public function getQueryAggregatedKey(): string
    {
        return $this->queryAggregatedKey;
    }

    public function getRelatedClassName(): ?string
    {
        return $this->name === 'tags' ? TagDemo::class : \stdClass::class;
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