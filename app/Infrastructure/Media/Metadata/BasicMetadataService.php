<?php

namespace App\Infrastructure\Media\Metadata;

use App\Domain\Media\Contract\MediaMetadataServiceInterface;
use App\Domain\Media\Media;

final class BasicMetadataService implements MediaMetadataServiceInterface
{
    public function enrich(Media $media): Media
    {
        $meta = $media->metadata();

        $url  = (string) $media->sourceUrl();
        $host = parse_url($url, PHP_URL_HOST) ?: null;
        $ext  = pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: null;

        $meta['source_host'] = $host;
        $meta['extension']   = $ext ? mb_strtolower($ext) : null;
        $meta['checksum']    = sha1($url);

        return $media->withMetadata($meta);
    }
}
