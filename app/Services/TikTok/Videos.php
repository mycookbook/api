<?php

namespace App\Services\TikTok;

use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpClient\Exception\InvalidArgumentException;

class Videos extends Request
{
    public function handle(): void
    {
        $nextRequest = $this->httpClient->request('POST',
            'https://open.tiktokapis.com/v2/video/list/',
            [
                'json' => [
                    'access_token' => Config::get('tiktok')['access_token'],
                    'fields' => [
                        'create_time',
                        'cover_image_url',
                        'share_url',
                        'video_description',
                        'duration',
                        'height',
                        'width',
                        'title',
                        'embed_html',
                        'embed_link',
                        'like_count',
                        'comment_count',
                        'share_count',
                        'view_count'
                    ],
                ],
            ],
        );

        $decoded = json_decode($nextRequest->getBody()->getContents(), true);

        if ($decoded["error"]) {
            $stage = '/video/list/';
            if ($exception = json_encode($decoded)) {
                throw new InvalidArgumentException($exception);
            }
        }
    }
}
