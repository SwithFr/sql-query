<?php

namespace SwithFr\Tests\DemoRelations;

use stdClass;
use SwithFr\SqlQuery\Relationship;
use SwithFr\SqlQuery\Contracts\RelationshipInterface;
use SwithFr\Tests\DemoEntities\TagDemo;

class ProductHaveManyTags extends Relationship
{
    public function __construct(protected string $name = 'tags', protected string $queryAggregatedKey = '_tags')
    {
    }

    public function getRelatedClassName(): ?string
    {
        return $this->name === 'tags' ? TagDemo::class : stdClass::class;
    }

    public function hasMany(): bool
    {
        return true;
    }

    public function getJoinQuery(): string
    {
        return 'left join product_tag on product_id = products.id left join tags tags on tag_id = t.id';
    }

    public function getRelatedTable(): string
    {
        return 'tags';
    }
}