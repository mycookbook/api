<?php

namespace Unit\Dtos;

use App\Dtos\TikTokUserDto;
use PHPUnit\Framework\TestCase;

class TikTokUserDtoTest extends TestCase
{
    protected TikTokUserDto $tiktokUserDto;

    public function setUp(): void
    {
        parent::setUp();

        $this->tiktokUserDto = new TikTokUserDto(
            1,
            'test',
            'test',
            false,
            'test',
            'test',
            'test',
            'test',
            'test',
            'test',
            'test',
            0
        );
    }

    /**
     * @test
     */
    public function it_can_get()
    {
        $this->assertSame([
            'user_id' => 1,
            'open_id' => 'test',
            'code' => 'test',
            'is_verified' => false,
            'profile_deep_link' => 'test',
            'bio_description' => 'test',
            'display_name' => 'test',
            'avatar_large_url' => 'test',
            'avatar_url_100' => 'test',
            'avatar_url' => 'test',
            'union_id' => 'test',
            'video_count' => 0
        ], $this->tiktokUserDto->toArray());
    }
}
