<?php
namespace App\Domain\Article\DTO;

use App\Domain\Article\MediaAttachment;

final class CreateArticleRequest
{
    /** @param list<string> $imageUuidList
     *  @param list<MediaAttachment> $mediaAttachments
     */
    public function __construct(
        public readonly string $headline,
        public readonly string $content,
        public readonly array $imageUuidList = [],
        public readonly array $mediaAttachments = [],
    ) {}
}
