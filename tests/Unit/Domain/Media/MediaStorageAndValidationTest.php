<?php

namespace Tests\Unit\Domain\Media;

use PHPUnit\Framework\TestCase;
use App\Domain\Media\Media;
use App\Domain\Media\MediaType;
use App\Domain\Shared\Url;
use App\Domain\Media\Service\MediaService;
use App\Infrastructure\Media\Validation\AllowedTypesValidator;
use App\Infrastructure\Media\Repository\InMemoryMediaRepository;
use App\Domain\Media\DTO\SearchCriteria;

final class MediaStorageAndValidationTest extends TestCase
{
    public function test_store_valid_media_and_retrieve_it(): void
    {
        $repo = new InMemoryMediaRepository();
        $validator = new AllowedTypesValidator([
            MediaType::IMAGE, MediaType::VIDEO, MediaType::AUDIO, MediaType::GRAPH, MediaType::FILE,
        ]);

        // Null metadata enrich (no changes)
        $nullMetadata = new class implements \App\Domain\Media\Contract\MediaMetadataServiceInterface {
            public function enrich(\App\Domain\Media\Media $media): \App\Domain\Media\Media { return $media; }
        };

        $service = new MediaService($repo, $validator, $nullMetadata);

        $media = Media::create(
            MediaType::IMAGE,
            'Cover',
            'Front cover',
            Url::fromString('https://example.com/cover.jpg')
        );

        $stored = $service->store($media, enrich: false);

        // can fetch by id
        $fetched = $repo->get($stored->id());
        $this->assertNotNull($fetched);
        $this->assertSame($stored->id()->toString(), $fetched->id()->toString());
        $this->assertSame('Cover', $fetched->title());

        // can search by type and title substring
        $hits = $repo->search(new SearchCriteria(type: MediaType::IMAGE, titleContains: 'cov'));
        $this->assertCount(1, $hits);
        $this->assertSame('Cover', $hits[0]->title());
    }

    public function test_validator_rejects_disallowed_type_on_store(): void
    {
        $repo = new InMemoryMediaRepository();
        // Only allow IMAGE; reject VIDEO
        $validator = new AllowedTypesValidator([MediaType::IMAGE]);

        $nullMetadata = new class implements \App\Domain\Media\Contract\MediaMetadataServiceInterface {
            public function enrich(\App\Domain\Media\Media $media): \App\Domain\Media\Media { return $media; }
        };

        $service = new MediaService($repo, $validator, $nullMetadata);

        $video = Media::create(
            MediaType::VIDEO,
            'Teaser',
            'Short clip',
            Url::fromString('https://example.com/clip.mp4')
        );

        $this->expectException(\Throwable::class); // adjust to your validator’s exact exception if needed
        $service->store($video, enrich: false);
    }
}
