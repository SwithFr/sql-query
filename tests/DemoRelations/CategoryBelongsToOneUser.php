<?php

namespace SwithFr\Tests\DemoRelations;

use SwithFr\SqlQuery\Relationship;
use SwithFr\SqlQuery\Contracts\RelationshipInterface;
use SwithFr\Tests\DemoEntities\UserDemo;

class CategoryBelongsToOneUser extends Relationship
{
    public function __construct(
        protected string $name = 'category.user',
        protected string $queryAggregatedKey = '_category_user'
    ) {
    }

    public function getRelatedClassName(): ?string
    {
        return UserDemo::class;
    }

    public function getRelatedTable(): string
    {
        return 'users';
    }

    public function getJoinQuery(): string
    {
        return 'left join users on users.id = categories.id';
    }
}