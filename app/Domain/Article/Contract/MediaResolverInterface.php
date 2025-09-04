<?php
namespace App\Domain\Article\Contract;

use App\Domain\Article\Article;

interface MediaResolverInterface
{
    /** @return array{
     *   images:list<\App\Domain\Media\Media>,
     *   attachments:list<\App\Domain\Media\Media>
     * } */
    public function resolveFor(Article $article): array;
}
