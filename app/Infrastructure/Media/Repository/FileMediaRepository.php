<?php
// app/Infrastructure/Media/Repository/FileMediaRepository.php
namespace App\Infrastructure\Media\Repository;

use App\Domain\Media\Contract\MediaRepositoryInterface;
use App\Domain\Media\DTO\SearchCriteria;
use App\Domain\Media\Media;
use App\Domain\Shared\MediaId;
use Illuminate\Support\Facades\Storage;

final class FileMediaRepository implements MediaRepositoryInterface
{
    private string $path = 'media_store.json';

    /** @return array<string,array<string,mixed>> */
    private function readAll(): array {
        if (!Storage::disk('local')->exists($this->path)) return [];
        $json = Storage::disk('local')->get($this->path);
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    /** @param array<string,array<string,mixed>> $all */
    private function writeAll(array $all): void {
        Storage::disk('local')->put($this->path, json_encode($all, JSON_PRETTY_PRINT));
    }

    public function save(Media $media): void {
        $all = $this->readAll();
        $all[$media->id()->toString()] = MediaMapper::toArray($media);
        $this->writeAll($all);
    }

    public function get(MediaId $id): ?Media {
        $row = $this->readAll()[$id->toString()] ?? null;
        return $row ? MediaMapper::fromArray($row) : null;
    }

    /** @return list<Media> */
    public function search(SearchCriteria $criteria): array {
        $rows = array_values($this->readAll());
        if ($criteria->type) {
            $rows = array_filter($rows,
             fn($r) => ($r['type'] ?? null) === $criteria->type->value);
        }
        if ($criteria->titleContains) {
            $needle = mb_strtolower($criteria->titleContains);
            $rows = array_filter($rows,
            fn($r) => str_contains(mb_strtolower((string)($r['title'] ?? '')), $needle));
        }
        return array_values(array_map(fn($r) => MediaMapper::fromArray($r), $rows));
    }
}
