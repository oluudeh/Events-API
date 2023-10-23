<?php

namespace App\Validators;

enum Rule
{
    case Required;
    case PresentAndFutureDate;
}
