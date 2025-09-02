<?php

namespace App\Infrastructure\Media\Repository;

use App\Domain\Media\Media;
use App\Domain\Media\MediaType;
use App\Domain\Shared\MediaId;
use App\Domain\Shared\Url;

final class MediaMapper {
    /** @return array<string,mixed> */
    public static function toArray(Media $m): array {
        return [
            'uuid'        => $m->id()->toString(),
            'type'        => $m->type()->value,
            'title'       => $m->title(),
            'description' => $m->description(),
            'source_url'  => (string) $m->sourceUrl(),
            'uploaded_at' => $m->uploadedAt()->format(DATE_ATOM),
            'metadata'    => $m->metadata(),
        ];
    }
    /** @param array<string,mixed> $row */
    public static function fromArray(array $row): Media {
        return Media::reconstitute(
            MediaId::fromString($row['uuid']),
            MediaType::from($row['type']),
            (string)$row['title'],
            (string)$row['description'],
            Url::fromString((string)$row['source_url']),
            new \DateTimeImmutable((string)$row['uploaded_at']),
            is_array($row['metadata'] ?? null) ? $row['metadata'] : []
        );
    }
}
