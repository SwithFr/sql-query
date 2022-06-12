<?php

namespace SwithFr\SqlQuery;

class SqlEntity
{
    use ArrayHydratation;

    public function __construct(array $data = [])
    {
        foreach($data as $key => $value){
            $this->{$key} = $value;
        }
    }
}