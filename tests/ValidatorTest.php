<?php

use App\Exceptions\ValidationException;
use App\Validators\InputValidator;
use App\Validators\PresentAndFutureDateValidator;
use App\Validators\Rule;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{

    public function setUp(): void
    {
        InputValidator::registerRuleHandler(Rule::PresentAndFutureDate, new PresentAndFutureDateValidator);
    }

    public function testValidateCorrectFutureDate()
    {
        $result = InputValidator::validate(
            rules: [
                'date' => Rule::PresentAndFutureDate
            ],
            inputs: [
                'date' => '2024-10-23'
            ],
        );
        $this->assertTrue($result);
    }

    public function testValidateIncorrectDate()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        InputValidator::validate(
            rules: [
                'date' => Rule::PresentAndFutureDate
            ],
            inputs: [
                'date' => '2024-02-30'
            ],
        );
    }


    public function testValidatePastDate()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        InputValidator::validate(
            rules: [
                'date' => Rule::PresentAndFutureDate
            ],
            inputs: [
                'date' => '2023-02-01'
            ],
        );
    }

}