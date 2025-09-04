<?php
namespace App\Infrastructure\Article\Repository;

use App\Domain\Article\Article;
use App\Domain\Article\Contract\ArticleRepositoryInterface;
use App\Domain\Shared\ArticleId;
use Illuminate\Support\Facades\Storage;

final class FileArticleRepository implements ArticleRepositoryInterface
{
    private string $disk;
    private string $path;

    public function __construct()
    {
        $this->disk = (string) config('article.file.disk', 'local');
        $this->path = (string) config('article.file.path', 'articles_store.json');
    }

    /** @return array<string,array<string,mixed>> */
    private function readAll(): array
    {
        if (!Storage::disk($this->disk)->exists($this->path)) return [];
        $json = Storage::disk($this->disk)->get($this->path);
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    /** @param array<string,array<string,mixed>> $all */
    private function writeAll(array $all): void
    {
        Storage::disk($this->disk)->put($this->path, json_encode($all, JSON_PRETTY_PRINT));
    }

    public function save(Article $article): void
    {
        $all = $this->readAll();
        /** @var array<string,mixed> $row */
        $row = $article->jsonSerialize();
        $all[$row['article_uuid']] = $row;
        $this->writeAll($all);
    }

    public function get(ArticleId $id): ?Article
    {
        $row = $this->readAll()[$id->toString()] ?? null;
        return $row ? Article::fromArray($row) : null;
    }
}
