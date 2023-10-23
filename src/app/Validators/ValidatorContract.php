<?php

namespace App\Validators;

interface ValidatorContract
{
    public function validate(mixed $value): void;
}