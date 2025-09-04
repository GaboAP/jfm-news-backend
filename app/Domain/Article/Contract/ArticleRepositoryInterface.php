<?php
namespace App\Domain\Article\Contract;

use App\Domain\Article\Article;
use App\Domain\Shared\ArticleId;

interface ArticleRepositoryInterface
{
    public function save(Article $article): void;
    public function get(ArticleId $id): ?Article;
}
