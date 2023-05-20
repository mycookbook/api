<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\StaticContent;

class StaticContentController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get()
    {
        /** @var \Illuminate\Database\Eloquent\Builder $cookie_policy */
        $cookie_policy = StaticContent::where('title', 'cookie-policy');

        /** @var \Illuminate\Database\Eloquent\Builder $usage_policy */
        $usage_policy = StaticContent::where('title', 'usage-policy');

        /** @var \Illuminate\Database\Eloquent\Builder $dr_policy */
        $dr_policy = StaticContent::where('title', 'data-retention-policy');

        /** @var \Illuminate\Database\Eloquent\Builder $tnc */
        $tnc = StaticContent::where('title', 'terms-and-conditions');

        return response()->json([
            'response' => [
                'cookiePolicy' => $cookie_policy->first(),
                'usagePolicy' => $usage_policy->first(),
                'dataRetentionPolicy' => $dr_policy->first(),
                'termsAndConditions' => $tnc->first(),
            ],
        ]);
    }
}
