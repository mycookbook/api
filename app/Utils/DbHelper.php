<?php

declare(strict_types=1);

namespace App\Utils;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DbHelper
{
    /**
     * @param string $value
     * @param string $table
     * @param string $column
     * @return string
     */
    public static function generateUniqueSlug(string $value, string $table, string $column): string
    {
        $slugified = Str::slug($value);
        $result = DB::table($table)->where($column, '=', $slugified)->first();

        if ($result === null) {
            return $slugified;
        }

        //try to regenerate with entity name or title, default to the given value
        if (isset($result->name)) {
            $value = $result->name;
        } else if (isset($result->title)) {
            $value = $result->title;
        }

        return self::generateUniqueSlug(Str::lower($value . '-' . time()), $table, $column);
    }
}
