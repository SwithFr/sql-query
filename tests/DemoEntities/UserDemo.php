<?php

namespace SwithFr\Tests\DemoEntities;

class UserDemo
{
    public int $id;

    public string $name;

    /**
     * @var \SwithFr\Tests\DemoEntities\ProductDemo[]
     */
    public array $products;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function getProducts(): array
    {
        return $this->products;
    }
}