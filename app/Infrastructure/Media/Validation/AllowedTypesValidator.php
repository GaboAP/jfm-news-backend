<?php

namespace App\Infrastructure\Media\Validation;

use App\Domain\Media\Contract\MediaValidatorInterface;
use App\Domain\Media\Media;
use App\Domain\Media\MediaType;

final class AllowedTypesValidator implements MediaValidatorInterface
{
    /** @param list<MediaType> $allowed */
    public function __construct(
        private array $allowed = [
            MediaType::IMAGE,
            MediaType::VIDEO,
            MediaType::AUDIO,
            MediaType::GRAPH,
            MediaType::FILE,
        ]
    ) {}

    public function validate(Media $media): void
    {
        if (!in_array($media->type(), $this->allowed, true)) {
            throw new \DomainException('Unsupported media type: '.$media->type()->value);
        }
        if ($media->title() === '') {
            throw new \DomainException('Title cannot be empty');
        }
        // add more rules here as needed (lengths, URL host whitelist, etc.)
    }
}
