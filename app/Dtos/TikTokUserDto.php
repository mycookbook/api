<?php

declare(strict_types=1);

namespace App\Dtos {

    readonly class TikTokUserDto
    {
        public int $user_id;
        public string $open_id;
        public string $code;
        public bool $is_verified;
        public string $profile_deep_link;
        public string $bio_description;
        public string  $display_name;
        public string $avatar_large_url;
        public string $avatar_url_100;
        public string $avatar_url;
        public string $union_id;
        public int $video_count;

        public function __construct(
            int $user_id,
            string $open_id,
            string $code,
            bool $is_verified,
            string $profile_deep_link,
            string $bio_description,
            string $display_name,
            string $avatar_large_url,
            string $avatar_url_100,
            string $avatar_url,
            string $union_id,
            int $video_count
        ) {
            $this->user_id = $user_id;
            $this->open_id = $open_id;
            $this->code = $code;
            $this->is_verified = $is_verified;
            $this->profile_deep_link = $profile_deep_link;
            $this->bio_description = $bio_description;
            $this->display_name = $display_name;
            $this->avatar_large_url = $avatar_large_url;
            $this->avatar_url_100 = $avatar_url_100;
            $this->avatar_url = $avatar_url;
            $this->union_id = $union_id;
            $this->video_count = $video_count;
        }

        public function toArray(): array
        {
            return [
                'user_id' => $this->user_id,
                'open_id' => $this->open_id,
                'code' => $this->code,
                'is_verified' => $this->is_verified,
                'profile_deep_link' => $this->profile_deep_link,
                'bio_description' => $this->bio_description,
                'display_name' => $this->display_name,
                'avatar_large_url' => $this->avatar_large_url,
                'avatar_url_100' => $this->avatar_url_100,
                'avatar_url' => $this->avatar_url,
                'union_id' => $this->union_id,
                'video_count' => $this->video_count
            ];
        }
    }
}
