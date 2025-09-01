<?php

namespace App\Domain\Media\Contract;

use App\Domain\Media\Media;

interface MediaValidatorInterface
{
    /** @throws \DomainException */
    public function validate(Media $media): void;
}
