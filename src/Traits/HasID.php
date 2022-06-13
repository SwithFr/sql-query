<?php

namespace SwithFr\SqlQuery\Traits;

trait HasID
{
    public int $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}