<?php

namespace App\Entities;

/**
 * Parent class of all entities.
 */
abstract class BaseEntity
{

    /**
     * Extracts table name from Entity class.
     */
    public static function tableName(): string
    {
        $class = new \ReflectionClass(get_called_class());
        return strtolower($class->getShortName());
    }

    protected int $id;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

}
