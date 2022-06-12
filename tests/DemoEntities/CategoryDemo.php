<?php

namespace SwithFr\Tests\DemoEntities;

use SwithFr\SqlQuery\SqlEntity;

class CategoryDemo extends SqlEntity
{
    public int $id;

    public string $name;

    public ?int $user_id = null;

    public ?UserDemo $user = null;

    public function getUser(): ?UserDemo
    {
        return $this->user;
    }
}