<?php

namespace SwithFr\SqlQuery\Contracts;

interface RelationshipInterface
{
    /**
     * The relation name, nested relations must be separated by a dot.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the key used in query to aggregate relation data.
     *
     * @return string
     */
    public function getQueryAggregatedKey(): string;

    /**
     * Related data should be mapped into the given class name.
     *
     * @return string|null
     */
    public function getRelatedClassName(): ?string;

    /**
     * Item has many related
     *
     * @return bool
     */
    public function hasMany(): bool;
}