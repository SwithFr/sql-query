<?php

namespace SwithFr\Tests\DemoRelations;

use stdClass;
use SwithFr\SqlQuery\Relationship;
use SwithFr\SqlQuery\Contracts\RelationshipInterface;
use SwithFr\Tests\DemoEntities\CategoryDemo;

class ProductHaveOneCategory extends Relationship
{
    public function __construct(protected string $name = 'category', protected string $queryAggregatedKey = '_category')
    {
    }

    public function getRelatedClassName(): ?string
    {
        return $this->name === 'category' ? CategoryDemo::class : stdClass::class;
    }

    public function getJoinQuery(): string
    {
        return 'left join categories on products.category_id = categories.id';
    }

    public function getRelatedTable(): string
    {
        return 'categories';
    }
}