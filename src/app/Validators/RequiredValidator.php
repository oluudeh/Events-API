<?php

namespace App\Validators;

class RequiredValidator implements ValidatorContract
{
    public function validate(mixed $value): void
    {
        if ($value === null || $value === "") {
            throw new \UnexpectedValueException("This field is required");
        }
    }
}
