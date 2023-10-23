<?php

namespace App\Validators;

use DateTime;

class PresentAndFutureDateValidator implements ValidatorContract
{
    public function validate(mixed $value): void
    {
        if (!$value) {
            return;
        }
        $date = DateTime::createFromFormat('Y-m-d', $value);

        if (!$date || $date->format('Y-m-d') !== $value) {
            throw new \UnexpectedValueException("Unsupported date format. Use YYYY-MM-DD format.");
        }

        $today = new DateTime();

        if ($date < $today) {
            throw new \UnexpectedValueException("Only allows present and future dates");
        }
    }
}