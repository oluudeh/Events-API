<?php

namespace App\Entities;

class City extends BaseEntity
{
    protected string $name;

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

}