<?php

namespace App\Domain\Media;

use App\Domain\Shared\MediaId;
use App\Domain\Shared\Url;
use DateTimeImmutable;

/**
 * Immutable, dependency-free domain entity.
 */
final class Media implements \JsonSerializable
{
    /** @param array<string,mixed> $metadata */
    private function __construct(
        private MediaId $id,
        private MediaType $type,
        private string $title,
        private string $description,
        private Url $sourceUrl,
        private DateTimeImmutable $uploadedAt,
        private array $metadata = [],
    ) {}

    /**
     * Named constructor keeps invariants in one place.
     * @param array<string,mixed> $metadata
     */
    public static function create(
        MediaType $type,
        string $title,
        string $description,
        Url $sourceUrl,
        ?DateTimeImmutable $uploadedAt = null,
        array $metadata = [],
    ): self {
        $title = trim($title);
        $description = trim($description);

        if ($title === '') {
            throw new \DomainException('Title cannot be empty');
        }

        return new self(
            MediaId::new(),
            $type,
            $title,
            $description,
            $sourceUrl,
            $uploadedAt ?? new DateTimeImmutable('now'),
            $metadata
        );
    }

    // -------- Getters (no setters to keep immutability)
    public function id(): MediaId { return $this->id; }
    public function type(): MediaType { return $this->type; }
    public function title(): string { return $this->title; }
    public function description(): string { return $this->description; }
    public function sourceUrl(): Url { return $this->sourceUrl; }
    public function uploadedAt(): DateTimeImmutable { return $this->uploadedAt; }
    /** @return array<string,mixed> */
    public function metadata(): array { return $this->metadata; }

    // -------- "Withers" to evolve state immutably
    public function withTitle(string $title): self
    {
        $clone = clone $this;
        $title = trim($title);
        if ($title === '') {
            throw new \DomainException('Title cannot be empty');
        }
        $clone->title = $title;
        return $clone;
    }

    /** @param array<string,mixed> $metadata */
    public function withMetadata(array $metadata): self
    {
        $clone = clone $this;
        $clone->metadata = $metadata;
        return $clone;
    }

    // -------- Serialization helpers (handy for CLI/HTTP later)
    /** @return array<string,mixed> */
    public function jsonSerialize(): array
    {
        return [
            'uuid'        => $this->id()->toString(),
            'type'        => $this->type()->value,
            'title'       => $this->title(),
            'description' => $this->description(),
            'source_url'  => $this->sourceUrl()->toString(),
            'uploaded_at' => $this->uploadedAt()->format(DATE_ATOM),
            'metadata'    => $this->metadata(),
        ];
    }
}
