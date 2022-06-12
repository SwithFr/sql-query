<?php

namespace SwithFr\Tests\DemoEntities;

use SwithFr\SqlQuery\SqlEntity;

class TagDemo extends SqlEntity
{
    public int $id;

    public string $name;
}