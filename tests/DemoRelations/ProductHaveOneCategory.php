<?php

namespace SwithFr\Tests\DemoRelations;

use SwithFr\SqlQuery\Contracts\RelationshipInterface;
use SwithFr\Tests\DemoEntities\CategoryDemo;

class ProductHaveOneCategory implements RelationshipInterface
{
    public function __construct(private bool $toStdClass = false)
    {
    }

    public function getQueryAggregatedKey(): string
    {
        return $this->toStdClass ? '_cat' : '_category';
    }

    public function getRelatedClassName(): ?string
    {
        return $this->toStdClass ? \stdClass::class : CategoryDemo::class;
    }

    public function hasMany(): bool
    {
        return false;
    }
}