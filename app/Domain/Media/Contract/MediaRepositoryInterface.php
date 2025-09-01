<?php

namespace App\Domain\Media\Contract;

use App\Domain\Media\DTO\SearchCriteria;
use App\Domain\Media\Media;
use App\Domain\Shared\MediaId;

interface MediaRepositoryInterface
{
    public function save(Media $media): void;
    public function get(MediaId $id): ?Media;

    /** @return list<Media> */
    public function search(SearchCriteria $criteria): array;
}
