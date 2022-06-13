<?php

namespace SwithFr\SqlQuery\Traits;

trait ArrayHydratation
{
    public function hydrate(array $data = []): void
    {
        foreach($data as $key => $value){
            $this->{$key} = $value;
        }
    }
}