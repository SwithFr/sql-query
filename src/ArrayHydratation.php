<?php

namespace SwithFr\SqlQuery;

trait ArrayHydratation
{
    public function hydrate(array $data = []): void
    {
        foreach($data as $key => $value){
            $this->{$key} = $value;
        }
    }
}