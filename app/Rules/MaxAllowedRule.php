<?php


namespace App\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;

class MaxAllowedRule implements ImplicitRule
{
	/**
	 * @var
	 */
	private $maxAllowed;

	/**
	 * MaxAllowedRule constructor.
	 * @param $max
	 */
	public function __construct($max)
	{
		$this->maxAllowed = $max;
	}

	/**
	 * @param string $attribute
	 * @param mixed $value
	 * @return false
	 */
	public function passes($attribute, $value)
	{
		$values = explode(",", $value);

		return !(count($values) > $this->maxAllowed);
	}

	/**
	 * @return string
	 */
	public function message()
	{
		return 'The :attribute cannot exceed ' . $this->maxAllowed . '.';
	}
}