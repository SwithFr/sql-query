<?php

namespace SwithFr\Tests\DemoEntities;

use SwithFr\SqlQuery\SqlEntity;
use SwithFr\SqlQuery\Traits\HasID;
use SwithFr\SqlQuery\Traits\HasDeletedAt;

class ProductDemo extends SqlEntity
{
    use HasID, HasDeletedAt;

    public string $name;

    public CategoryDemo $category;

    public object $cat;

    /**
     * @var \SwithFr\Tests\DemoEntities\TagDemo[]
     */
    public array $tags;

    /**
     * @var \stdClass[]
     */
    public array $tagsstdclass;

    public function getCategory(): ?CategoryDemo
    {
        return $this->category;
    }

    public function getCat(): object
    {
        return $this->cat;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getTagsStdClass(): array
    {
        return $this->tagsstdclass;
    }
}