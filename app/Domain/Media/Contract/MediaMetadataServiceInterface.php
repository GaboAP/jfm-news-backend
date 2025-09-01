<?php

namespace App\Domain\Media\Contract;

use App\Domain\Media\Media;

interface MediaMetadataServiceInterface
{
    /** Return a new Media instance with enriched metadata */
    public function enrich(Media $media): Media;
}
