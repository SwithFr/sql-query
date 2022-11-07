<?php

namespace SwithFr\SqlQuery\Traits;

use DateTime;
use DateTimeInterface;

trait HasCreatedAt
{
    public string $created_at;

    public function getCreatedAt(): DateTimeInterface
    {
        return new DateTime($this->created_at);
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->created_at = $createdAt->format('Y-m-d H:i:s');

        return $this;
    }
}