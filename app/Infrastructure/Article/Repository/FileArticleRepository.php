<?php
namespace App\Infrastructure\Article\Repository;

use App\Domain\Article\Article;
use App\Domain\Article\Contract\ArticleRepositoryInterface;
use App\Domain\Shared\ArticleId;
use Illuminate\Support\Facades\Storage;

final class FileArticleRepository implements ArticleRepositoryInterface
{
    private string $path = 'articles_store.json';

    /** @return array<string,array<string,mixed>> keyed by article_uuid */
    private function readAll(): array
    {
        if (!Storage::disk('local')->exists($this->path)) return [];
        $json = Storage::disk('local')->get($this->path);
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    /** @param array<string,array<string,mixed>> $all */
    private function writeAll(array $all): void
    {
        Storage::disk('local')->put($this->path, json_encode($all, JSON_PRETTY_PRINT));
    }

    public function save(Article $article): void
    {
        $all = $this->readAll();
        /** @var array<string,mixed> $row */
        $row = ArticleMapper::toArray($article);
        $all[$row['article_uuid']] = $row;
        $this->writeAll($all);
    }

    public function get(ArticleId $id): ?Article
    {
        $all = $this->readAll();
        $row = $all[$id->toString()] ?? null;
        return $row ? ArticleMapper::fromArray($row) : null;
    }
}
