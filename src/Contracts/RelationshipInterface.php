<?php

namespace SwithFr\SqlQuery\Contracts;

interface RelationshipInterface
{
    public function getQueryAggregatedKey(): string;

    public function getRelatedClassName(): ?string;

    public function hasMany(): bool;
}