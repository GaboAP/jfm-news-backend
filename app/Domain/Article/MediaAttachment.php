<?php
namespace App\Domain\Article;

use App\Domain\Media\MediaType;
use App\Domain\Shared\MediaId;

final class MediaAttachment implements \JsonSerializable
{
    public function __construct(
        private MediaId $mediaId,
        private MediaType $type,
        private ?string $label = null,     //  e.g. "gallery", "hero"
        private array $extra = [],          // optional: any extra per-attachment metadata
    ) {}

    public function mediaId(): MediaId { return $this->mediaId; }
    public function type(): MediaType { return $this->type; }
    public function label(): ?string { return $this->label; }
    /** @return array<string,mixed> */
    public function extra(): array { return $this->extra; }

    /** @return array{uuid:string,type:string,label:?string,extra:array} */
    public function jsonSerialize(): array
    {
        return [
            'uuid'  => $this->mediaId->toString(),
            'type'  => $this->type->value,
            'label' => $this->label,
            'extra' => $this->extra,
        ];
    }

    /** @param array{uuid:string,type:string,label?:?string,extra?:array} $row */
    public static function fromArray(array $row): self
    {
        return new self(
            MediaId::fromString($row['uuid']),
            MediaType::from($row['type']),
            $row['label'] ?? null,
            is_array($row['extra'] ?? null) ? $row['extra'] : []
        );
    }
}
