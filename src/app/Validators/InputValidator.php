<?php

namespace App\Validators;

use App\Validators\ValidatorContract;
use App\Exceptions\ValidationException;
use UnexpectedValueException;

class InputValidator
{
    private  static array $rulesHandlers = [];

    public static function registerRuleHandler(Rule $rule, ValidatorContract $validator): void
    {
        self::$rulesHandlers[$rule->name] = $validator;
    }

    public static function validate(array $rules, array $inputs): bool
    {
        $errors = [];

        foreach ($rules as $key => $rulesSet) {
            if (!is_array($rulesSet)) {
                $rulesSet = [$rulesSet];
            }

            foreach ($rulesSet as $rule) {

                if (!isset(self::$rulesHandlers[$rule->name])) {
                    throw new UnexpectedValueException("The rule '{$rule->name}' has no specified handler.");
                }

                $validator = self::$rulesHandlers[$rule->name];

                try {
                    $validator->validate($inputs[$key] ?? null);
                } catch (UnexpectedValueException $e) {
                    $errors[$key][] = isset($errors[$key]) ? $e->getMessage() : [$e->getMessage()];
                }
            }
        }

        if ($errors) {
            throw new ValidationException("Validation failed", $errors);
        }
        return true;
    }

}
