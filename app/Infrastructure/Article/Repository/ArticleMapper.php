<?php
namespace App\Infrastructure\Article\Repository;

use App\Domain\Article\Article;
use App\Domain\Article\MediaAttachment;
use App\Domain\Shared\ArticleId;

final class ArticleMapper
{
    /** @return array<string,mixed> */
    public static function toArray(Article $a): array
    {
        return $a->jsonSerialize();
    }

    /** @param array<string,mixed> $row */
    public static function fromArray(array $row): Article
    {
        return Article::fromArray($row);
    }
}
