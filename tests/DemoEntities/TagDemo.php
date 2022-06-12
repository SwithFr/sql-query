<?php

namespace SwithFr\Tests\DemoEntities;

class TagDemo
{
    public int $id;

    public string $name;

    public function __construct(array $data = [])
    {
        foreach($data as $key => $value){
            $this->{$key} = $value;
        }
    }
}