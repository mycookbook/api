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
            ->first();

        if ($lonLat->lon !== null && $lonLat->lat !== null) {
            return sprintf("%s,%s", number_format($lonLat->lon, 4), number_format($lonLat->lat, 4));
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
                ->first();
        }

        return sprintf("%s", $country);
    }

    private static function isPrivate(string $ipAddress)
    {
        $privateIps = ["172.21.0.1"];

        return in_array($ipAddress, $privateIps);
    }
}
