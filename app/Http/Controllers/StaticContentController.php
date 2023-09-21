<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\StaticContent;
use Illuminate\Http\Request;

class StaticContentController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        if ($request->route() && $request->route()->getName() == 'getCategories') {
            return $this->successResponse(['response' => Category::all()]);
        } else {
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
                    'termsAndConditions' => $tnc->first()
                ],
            ]);
        }
    }
}
