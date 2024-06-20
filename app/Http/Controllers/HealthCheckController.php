<?php

namespace App\Http\Controllers;

use App\Dtos\HealthChecksDto;
use Illuminate\Http\JsonResponse;
use Spatie\CpuLoadHealthCheck\CpuLoadCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Checks\Result;
use Spatie\Health\Health;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;

class HealthCheckController extends Controller
{
    private Health $healthChecker;

    /**
     * @class HealthCheckerController
     */
    public function __construct(Health $healthChecker)
    {
        $this->healthChecker = $healthChecker;
    }

    /**
     * CookbooksHQ Health status checks
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(): JsonResponse
    {
       $this->registerChecks();

        return $this->successResponse(
            array_map(function ($check) {
                return $this->newHealthStatusDto($check->run(), $check->getName())->toArray();
            }, $this->healthChecker->registeredChecks()->toArray())
        );
    }

    /**
     * @return void
     */
    private function registerChecks(): void
    {
        $this->healthChecker->checks(
            [
                EnvironmentCheck::new()->expectEnvironment(getenv('APP_ENV')),
                UsedDiskSpaceCheck::new()
                    ->warnWhenUsedSpaceIsAbovePercentage(70)
                    ->failWhenUsedSpaceIsAbovePercentage(90),
                CpuLoadCheck::new()
                    ->failWhenLoadIsHigherInTheLast5Minutes(2.0)
                    ->failWhenLoadIsHigherInTheLast15Minutes(1.5),
                DatabaseCheck::new()
                    ->connectionName(getenv('DB_CONNECTION'))
            ]
        );
    }

    /**
     * @param Result $healthStatusResult
     * @param string $checkName
     * @return HealthChecksDto
     */
    private function newHealthStatusDto(Result $healthStatusResult, string $checkName): HealthChecksDto
    {
        return new HealthChecksDto(
            $checkName,
            $healthStatusResult->meta,
            $healthStatusResult->status->value,
            $healthStatusResult->notificationMessage,
            $healthStatusResult->shortSummary
        );
    }
}
