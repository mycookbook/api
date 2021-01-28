<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;

class DisallowedCharactersRule implements ImplicitRule
{
	/**
	 * @param string $attribute
	 * @param mixed $value
	 * @return bool
	 */
	public function passes($attribute, $value): bool
	{
		if (preg_match('/[\'^£$%&*.}{#~?><>,|=+¬-]/', $value)) {
			return false;
		}

		return true;
	}

	/**
	 * @return string
	 */
	public function message(): string
	{
		return 'The :attribute must not contain any special characters.';
	}
}