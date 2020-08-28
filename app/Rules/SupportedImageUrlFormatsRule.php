<?php

namespace App\Rules;

use App\Exceptions\NotAnImageException;
use Illuminate\Support\Facades\Validator;

class SupportedImageUrlFormatsRule
{
	public static function validate()
	{
		Validator::extend('img_url', function ($attribute, $value) {
			try {
				exif_imagetype($value);
				return true;
			} catch(\Exception $e) {
				return false;
			}
		});
	}
}
