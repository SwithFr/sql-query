<?php

namespace SwithFr\SqlQuery\Traits;

trait ArrayHydratation
{
    public function hydrate(array $data = []): self
    {
        foreach($data as $key => $value){
            $this->{$key} = $value;
        }

        return $this;
    }
}