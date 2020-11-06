<?php

namespace App\Http\Controllers;

use App\StaticContent;

class StaticContentController extends Controller
{
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function get()
	{
		$cookie_policy = StaticContent::where('title', 'cookie-policy');
		$usage_policy = StaticContent::where('title', 'usage-policy');
		$dr_policy = StaticContent::where('title', 'data-retention-policy');
		$tnc = StaticContent::where('title', 'terms-and-conditions');

		return response()->json([
			'response' => [
				'cookiePolicy' => $cookie_policy->get()->first(),
				'usagePolicy' => $usage_policy->get()->first(),
				'dataRetentionPolicy' => $dr_policy->get()->first(),
				'termsAndConditions' => $tnc->get()->first()
			]
		]);
	}
}