<?php

namespace SwithFr\SqlQuery\Traits;

use DateTime;
use DateTimeInterface;

trait HasUpdatedAt
{
    public string $updated_at;

    public function getUpdatedAt(): DateTimeInterface
    {
        return new DateTime($this->updated_at);
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updated_at = $updatedAt->format('Y-m-d H:i:s');

        return $this;
    }
}