<?php
namespace App\Domain\Article\Service;

use App\Domain\Article\Article;
use App\Domain\Article\Contract\MediaResolverInterface;
use App\Domain\Media\Contract\MediaRepositoryInterface;
use App\Domain\Shared\MediaId;

final class MediaResolverService implements MediaResolverInterface
{
    public function __construct(private MediaRepositoryInterface $mediaRepo) {}

    public function resolveFor(Article $article): array
    {
        // Resolve image_uuid_list
        $images = [];
        foreach ($article->imageUuidList() as $uuid) {
            $m = $this->mediaRepo->get(MediaId::fromString($uuid));
            if ($m) { $images[] = $m; }
        }

        // Resolve media_attachments (may include non-images)
        $attachments = [];
        foreach ($article->mediaAttachments() as $att) {
            $m = $this->mediaRepo->get($att->mediaId());
            if ($m) { $attachments[] = $m; }
        }

        return ['images' => $images, 'attachments' => $attachments];
    }
}
