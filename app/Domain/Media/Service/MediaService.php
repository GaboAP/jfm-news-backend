<?php

namespace App\Domain\Media\Service;

use App\Domain\Media\Contract\MediaMetadataServiceInterface;
use App\Domain\Media\Contract\MediaRepositoryInterface;
use App\Domain\Media\Contract\MediaValidatorInterface;
use App\Domain\Media\DTO\SearchCriteria;
use App\Domain\Media\Media;

final class MediaService
{
    public function __construct(
        private MediaRepositoryInterface $repo,
        private MediaValidatorInterface $validator,
        private MediaMetadataServiceInterface $metadata,
    ) {}

    public function store(Media $media): Media
    {
        $this->validator->validate($media);
        $media = $this->metadata->enrich($media);
        $this->repo->save($media);
        return $media;
    }

    /** @return list<Media> */
    public function search(SearchCriteria $criteria): array
    {
        return $this->repo->search($criteria);
    }
}
