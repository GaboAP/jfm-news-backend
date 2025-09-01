<?php

namespace App\Domain\Media\DTO;

use App\Domain\Media\MediaType;

final class SearchCriteria
{
    public function __construct(
        public readonly ?MediaType $type = null,
        public readonly ?string $titleContains = null
    ) {}
}
