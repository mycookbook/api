<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;

class JsonStructureRule implements ImplicitRule
{
    /**
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $specs = [
            'cal' => [
                'type' => 'integer',
                'unit' => null,
            ],
            'carbs' => [
                'type' => 'integer',
                'unit' => 'g',
            ],
            'protein' => [
                'type' => 'integer',
                'unit' => 'g',
            ],
            'fat' => [
                'tyoe' => 'integer',
                'unit' => 'g',
            ],
        ];

        $obj = json_decode($value);
        $given_keys = array_keys((array) $obj);
        $spec_keys = array_keys($specs);

        if ($given_keys !== $spec_keys) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return 'The :attribute is not a valid json.';
    }
}
