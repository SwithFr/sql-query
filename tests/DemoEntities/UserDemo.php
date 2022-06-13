<?php

namespace SwithFr\Tests\DemoEntities;

use SwithFr\SqlQuery\SqlEntity;
use SwithFr\SqlQuery\Traits\HasID;
use SwithFr\SqlQuery\Traits\HasUpdatedAt;

class UserDemo extends SqlEntity
{
    use HasID, HasUpdatedAt;

    public string $name;

    /**
     * @var \SwithFr\Tests\DemoEntities\ProductDemo[]
     */
    public array $products;

    public function getProducts(): array
    {
        return $this->products;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}