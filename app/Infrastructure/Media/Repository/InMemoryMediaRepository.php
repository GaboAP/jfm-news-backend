<?php
// app/Infrastructure/Media/Repository/InMemoryMediaRepository.php
namespace App\Infrastructure\Media\Repository;

use App\Domain\Media\Contract\MediaRepositoryInterface;
use App\Domain\Media\Media;
use App\Domain\Shared\MediaId;
use App\Domain\Media\DTO\SearchCriteria;

final class InMemoryMediaRepository implements MediaRepositoryInterface
{
    /** @var array<string, Media> */
    private array $store = [];

    public function save(Media $media): void
    {
        $this->store[$media->id()->toString()] = $media;
    }

    public function get(MediaId $id): ?Media
    {
        return $this->store[$id->toString()] ?? null;
    }

    public function search(SearchCriteria $criteria): array
    {
        $results = array_values($this->store);

        if ($criteria->type) {
            $results = array_filter($results, fn(Media $m) => $m->type() === $criteria->type);
        }
        if ($criteria->titleContains) {
            $needle = mb_strtolower($criteria->titleContains);
            $results = array_filter($results, fn(Media $m) => str_contains(mb_strtolower($m->title()), $needle));
        }
        return array_values($results);
    }
}
