<?php

namespace SwithFr\SqlQuery\Traits;

use DateTime;
use DateTimeInterface;

trait HasDeletedAt
{
    public ?string $deleted_at = null;

    public function getDeletedAt(): ?DateTimeInterface
    {
        return new DateTime($this->deleted_at);
    }

    public function setDeletedAt(?DateTimeInterface $deletedAt = null): self
    {
        $this->deleted_at = $deletedAt->format('Y-m-d H:i:s');

        return $this;
    }
}