<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;

class NutritionalDetailJsonStructure
{
	public static function validate(){

		Validator::extend('nutritional_detail_json_structure', function ($attribute, $value) {
			$specs = [
				"cal" => [
					"type" => "integer",
					"unit" => null
				],
				"carbs" => [
					"type" => "integer",
					"unit" => "g"
				],
				"protein" => [
					"type" => "integer",
					"unit" => "g"
				],
				"fat" => [
					"tyoe" => "integer",
					"unit" => "g"
				]
			];

			$obj = json_decode($value);
			$given_keys = array_keys((array) $obj);
			$spec_keys = array_keys($specs);

			if ($given_keys !== $spec_keys) {
				return false;
			}

			return true;
		});
	}
}