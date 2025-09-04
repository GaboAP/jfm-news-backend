<?php

namespace Tests\Unit\Domain\Media;

use PHPUnit\Framework\TestCase;
use App\Domain\Media\Media;
use App\Domain\Media\MediaType;
use App\Domain\Shared\Url;
use App\Infrastructure\Media\Metadata\BasicMetadataService;

final class MetadataEnrichmentTest extends TestCase
{
    public function test_basic_metadata_enrichment_adds_expected_fields(): void
    {
        $media = Media::create(
            MediaType::AUDIO,
            'TestAudio',
            'desc',
            Url::fromString('https://example.com/path/to/audio.mp3')
        );

        $svc = new BasicMetadataService();
        $enriched = $svc->enrich($media);
        $meta = $enriched->metadata();

        $this->assertArrayHasKey('source_host', $meta);
        $this->assertSame('example.com', $meta['source_host']);

        $this->assertArrayHasKey('extension', $meta);
        $this->assertSame('mp3', $meta['extension']);

        $this->assertArrayHasKey('checksum', $meta);
        $this->assertIsString($meta['checksum']);
        $this->assertNotSame('', $meta['checksum']);
        // If you compute SHA-1/other fixed hex, keep this; otherwise relax it:
        $this->assertMatchesRegularExpression('/^[a-f0-9]{40}$/i', $meta['checksum']);
    }
}
