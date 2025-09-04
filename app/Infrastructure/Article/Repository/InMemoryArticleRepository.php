<?php

namespace App\Infrastructure\Article\Repository;

use App\Domain\Article\Contract\ArticleRepositoryInterface;
use App\Domain\Article\Article;
use App\Domain\Shared\ArticleId;

final class InMemoryArticleRepository implements ArticleRepositoryInterface
{
    /** @var array<string,Article> keyed by article_uuid */
    private array $store = [];

    public function save(Article $article): void
    {
        $this->store[$article->id()->toString()] = $article;
    }

    public function get(ArticleId $id): ?Article
    {
        return $this->store[$id->toString()] ?? null;
    }
}
