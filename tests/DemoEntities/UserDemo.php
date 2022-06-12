<?php

namespace SwithFr\Tests\DemoEntities;

use SwithFr\SqlQuery\SqlEntity;

class UserDemo extends SqlEntity
{
    public int $id;

    public string $name;

    /**
     * @var \SwithFr\Tests\DemoEntities\ProductDemo[]
     */
    public array $products;

    public function getProducts(): array
    {
        return $this->products;
    }
}