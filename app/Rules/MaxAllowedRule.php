<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;

class MaxAllowedRule implements ImplicitRule
{
    private int $maxAllowed;

    /**
     * MaxAllowedRule constructor.
     *
     * @param $max
     */
    public function __construct($max)
    {
        $this->maxAllowed = $max;
    }

    public function passes($attribute, $value): bool
    {
        $values = explode(',', $value);

        return ! (count($values) > $this->maxAllowed);
    }

    /**
     * @return string
     */
    public function message()
    {
        return 'The :attribute cannot exceed '.$this->maxAllowed.'.';
    }
}
