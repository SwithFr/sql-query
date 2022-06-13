<?php

namespace SwithFr\SqlQuery;

use SwithFr\SqlQuery\Traits\ArrayHydratation;

class SqlEntity
{
    use ArrayHydratation;

    private array $_original = [];

    public function __construct(array $data = [])
    {
        $properties = get_object_vars($this);
        unset($properties['_original']);
        $this->_original = $data ?: $properties;
        $this->hydrate($data);
    }

    public function getOriginal(): array
    {
        return $this->_original;
    }
}