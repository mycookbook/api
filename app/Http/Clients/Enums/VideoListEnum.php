<?php

namespace App\Http\Clients\Enums;

enum VideoListEnum: string
{
    case COVER_IMAGE_URL = 'cover_image_url';
    case ID = 'id';
    case TITLE = 'title';
    case VIDEO_DESCRIPTION = 'video_description';
    case DURATION = 'duration';
    case HEIGHT = 'height';
    case WIDTH = 'width';
    case EMBED_HTML = 'embed_html';
    case EMBED_LINK = 'embed_link';

    public static function values(): array
    {
        return array_map(function($case) {
            return $case->value;
        }, self::cases());
    }
}
