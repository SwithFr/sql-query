<?php

namespace SwithFr\Tests\DemoEntities;

class CategoryDemo
{
    public int $id;

    public string $name;

    public ?int $user_id = null;

    public ?UserDemo $user = null;

    public function __construct(array $data = [])
    {
        foreach($data as $key => $value){
            $this->{$key} = $value;
        }
    }

    public function getUser(): ?UserDemo
    {
        return $this->user;
    }
}