<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;

class SupportedImageUrlFormatsRule implements ImplicitRule
{
    /**
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        try {
            exif_imagetype($value);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return 'The :attribute format is not supported';
    }
}
