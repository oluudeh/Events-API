<?php

namespace App\Exceptions;

/**
 * Custom Exception class for validation errors.
 */
class ValidationException extends \Exception
{
    public function __construct(
        protected $message,
        protected array $errors
    )
    {
        parent::__construct($message, 422);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}