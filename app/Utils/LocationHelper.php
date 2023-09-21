<?php

declare(strict_types=1);

namespace App\Utils;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LocationHelper
{
    public static function getLocFromIpAddress(string $ipAddress): string
    {
        if (self::isPrivate($ipAddress)) {
            return sprintf("%s,%s", number_format(0, 4), number_format(0, 4));
        }

        $sanitizeIp = Str::replace(".", "", $ipAddress);

        $lonLat = DB::table('ip2nation')
            ->leftJoin(
                'ip2nationCountries',
                'ip2nationCountries.iso_code_2',
                '=',
                'ip2nation.country'
            )
            ->where('ip2nation.ip', '=', $sanitizeIp)
            ->get(['ip2nationCountries.lon', 'ip2nationCountries.lat'])
            ->toArray();

        if (count($lonLat) > 0) {
            return sprintf(
                "%s,%s",
                number_format($lonLat[0]->lon, 4),
                number_format($lonLat[0]->lat, 4)
            );
        }

        return sprintf("%s,%s", "unknown", "unknown");
    }

    public static function getCountryCodeFromIpAddress(string $ipAddress): string
    {
        $sanitizeIp = Str::replace(".", "", $ipAddress);

        if (self::isPrivate($ipAddress)) {
            $country = "Private";
        } else {
            $country = DB::table('ip2nation')
                ->where('ip', '=', $sanitizeIp)
                ->pluck('country')
                ->toArray();
        }

        return sprintf("%s", $country[0]);
    }

    private static function isPrivate(string $ipAddress)
    {
        $privateIps = ["172.21.0.1", "0.0.0.0", "127.0.0.1", "192.169.0.1"];

        return in_array($ipAddress, $privateIps);
    }
}
