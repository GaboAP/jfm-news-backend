<?php

namespace Tests\Unit\Domain\Media;

use App\Domain\Media\Media;
use App\Domain\Media\MediaType;
use App\Domain\Shared\Url;
use PHPUnit\Framework\TestCase;

final class MediaTest extends TestCase
{
    public function test_create_and_immutability(): void
    {
        $media = Media::create(
            MediaType::IMAGE,
            'Cover photo',
            'Hero image for article',
            Url::fromString('https://example.com/cover.jpg')
        );

        $this->assertNotEmpty($media->id()->toString());
        $this->assertSame('image', $media->type()->value);
        $this->assertSame('Cover photo', $media->title());

        $updated = $media->withTitle('New title');
        $this->assertSame('Cover photo', $media->title()); // original unchanged
        $this->assertSame('New title', $updated->title());
    }

    public function test_empty_title_fails(): void
    {
        $this->expectException(\DomainException::class);

        Media::create(
            MediaType::VIDEO,
            '',
            'desc',
            Url::fromString('https://example.com/video.mp4')
        );
    }
}
