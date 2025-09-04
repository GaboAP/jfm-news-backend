<?php

namespace Tests\Unit\Domain\Article;

use PHPUnit\Framework\TestCase;
use App\Domain\Article\Article;
use App\Domain\Article\MediaAttachment;
use App\Domain\Article\Service\MediaResolverService;
use App\Domain\Media\Media;
use App\Domain\Media\MediaType;
use App\Domain\Shared\Url;
use App\Infrastructure\Media\Repository\InMemoryMediaRepository;

final class MediaResolutionTest extends TestCase
{
    public function test_resolves_images_and_attachments_to_real_media_objects(): void
    {
        // Seed media
        $mediaRepo = new InMemoryMediaRepository();

        $img = Media::create(
            MediaType::IMAGE, 'Hero', 'Lead image',
            Url::fromString('https://example.com/hero.jpg')
        );
        $vid = Media::create(
            MediaType::VIDEO, 'Clip', 'Teaser',
            Url::fromString('https://example.com/teaser.mp4')
        );

        $mediaRepo->save($img);
        $mediaRepo->save($vid);

        // Article referencing those UUIDs
        $article = Article::create(
            headline: 'Article with media',
            content: "Body",
            imageUuidList: [$img->id()->toString()],
            mediaAttachments: [
                new MediaAttachment($vid->id(), MediaType::VIDEO, 'inline')
            ]
        );

        // Resolve
        $resolver = new MediaResolverService($mediaRepo);
        $resolved = $resolver->resolveFor($article);

        // Assertions
        $this->assertCount(1, $resolved['images']);
        $this->assertCount(1, $resolved['attachments']);

        $this->assertSame($img->id()->toString(), $resolved['images'][0]->id()->toString());
        $this->assertSame('https://example.com/hero.jpg', (string) $resolved['images'][0]->sourceUrl());

        $this->assertSame($vid->id()->toString(), $resolved['attachments'][0]->id()->toString());
        $this->assertSame('https://example.com/teaser.mp4', (string) $resolved['attachments'][0]->sourceUrl());
    }
}
