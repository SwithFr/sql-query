<?php

namespace SwithFr\Tests\DemoRelations;

use SwithFr\SqlQuery\Contracts\RelationshipInterface;
use SwithFr\Tests\DemoEntities\TagDemo;

class ProductHaveManyTags implements RelationshipInterface
{
    public function __construct(private bool $toStdClass = false)
    {
    }

    public function getQueryAggregatedKey(): string
    {
        return $this->toStdClass ? '_tagsstdclass' : '_tags';
    }

    public function getRelatedClassName(): ?string
    {
        return $this->toStdClass ? \stdClass::class : TagDemo::class;
    }

    public function hasMany(): bool
    {
        return true;
    }
}