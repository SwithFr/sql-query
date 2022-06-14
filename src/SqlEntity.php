<?php

namespace SwithFr\SqlQuery;

use SwithFr\SqlQuery\Traits\ArrayHydratation;

class SqlEntity
{
    use ArrayHydratation;

    private array $_original;

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

    public function toArray(): array
    {
        try {
            $obj = json_encode(clone $this, JSON_THROW_ON_ERROR);
            return (array) json_decode($obj, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            dd("Error when converting entity {get_class()} to array");
        }
    }
}