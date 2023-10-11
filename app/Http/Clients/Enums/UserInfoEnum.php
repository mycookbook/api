<?php

namespace App\Http\Clients\Enums;

enum UserInfoEnum: string
{
    case OPEN_ID = 'open_id';
    case AVATAR_URL = 'avatar_url';
    case DISPLAY_NAME = 'display_name';
    case AVATAR_URL_100 = 'avatar_url_100';
    case IS_VERIFIED = 'is_verified';
    case PROFILE_DEEP_LINK = 'profile_deep_link';
    case BIO_DESCRIPTION = 'bio_description';
    case AVATAR_LARGE_URL = 'avatar_large_url';
    case UNION_ID = 'union_id';
    case VIDEO_COUNT =  'video_count';

    public static function values(): array
    {
        return array_map(function($case) {
            return $case->value;
        }, self::cases());
    }
}
