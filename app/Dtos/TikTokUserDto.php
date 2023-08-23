<?php

declare(strict_types=1);

namespace App\Dtos;

class TikTokUserDto
{
    protected $user_id;
    protected $open_id;
    protected $code;
    protected $is_verified;
    protected $profile_deep_link;
    protected $bio_description;
    protected $display_name;
    protected $avatar_large_url;
    protected $avatar_url_100;
    protected $avatar_url;
    protected $union_id;
    protected $video_count;

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

    public function getCode(): string
    {
        return $this->code;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getOpenId(): string
    {
        return $this->open_id;
    }

    public function getProfileDeepLink(): string
    {
        return $this->profile_deep_link;
    }

    public function getBioDescription(): string
    {
        return $this->bio_description;
    }

    public function getDisplayName(): string
    {
        return $this->display_name;
    }

    public function getAvatarLargeUrl(): string
    {
        return $this->avatar_large_url;
    }

    public function getAvatarUrl100(): string
    {
        return $this->avatar_url_100;
    }

    public function getAvatarUrl(): string
    {
        return $this->avatar_url;
    }

    public function getUnionId(): string
    {
        return $this->union_id;
    }

    public function getVideoCount(): int
    {
        return $this->video_count;
    }

    public function getIsVerified(): bool
    {
        return $this->is_verified;
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->getUserId(),
            'open_id' => $this->getOpenId(),
            'code' => $this->getCode(),
            'is_verified' => $this->getIsVerified(),
            'profile_deep_link' => $this->getProfileDeepLink(),
            'bio_description' => $this->getBioDescription(),
            'display_name' => $this->getDisplayName(),
            'avatar_large_url' => $this->getAvatarLargeUrl(),
            'avatar_url_100' => $this->getAvatarUrl100(),
            'avatar_url' => $this->getAvatarUrl(),
            'union_id' => $this->getUnionId(),
            'video_count' => $this->getVideoCount()
        ];
    }
}
