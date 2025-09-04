<?php
namespace App\Domain\Article;

use App\Domain\Shared\ArticleId;
use App\Domain\Shared\MediaId;
use DateTimeImmutable;

final class Article implements \JsonSerializable
{
    /** @param list<string> $imageUuidList
     *  @param list<MediaAttachment> $mediaAttachments
     */
    private function __construct(
        private ArticleId $id,
        private string $headline,
        private string $content,
        private DateTimeImmutable $createdAt,
        private array $imageUuidList = [],
        private array $mediaAttachments = [],
    ) {}

    /** @param list<string|MediaId> $imageUuidList
     *  @param list<MediaAttachment> $mediaAttachments
     */
    public static function create(
        string $headline,
        string $content,
        ?DateTimeImmutable $createdAt = null,
        array $imageUuidList = [],
        array $mediaAttachments = []
    ): self {
        $headline = trim($headline);
        if ($headline === '') {
            throw new \DomainException('Headline cannot be empty');
        }

        // normalize image UUIDs to strings
        $images = array_map(
            fn($v) => $v instanceof MediaId ? $v->toString() : (string) $v,
            $imageUuidList
        );

        return new self(
            ArticleId::new(),
            $headline,
            $content,
            $createdAt ?? new DateTimeImmutable('now'),
            $images,
            $mediaAttachments
        );
    }

    /** Rebuild from stored values (used by file-backed repo later) */
    /** @param list<string> $imageUuidList
     *  @param list<MediaAttachment> $mediaAttachments
     */
    public static function reconstitute(
        ArticleId $id,
        string $headline,
        string $content,
        DateTimeImmutable $createdAt,
        array $imageUuidList = [],
        array $mediaAttachments = []
    ): self {
        $self = new self($id, trim($headline), $content, $createdAt, $imageUuidList, $mediaAttachments);
        return $self;
    }

    // --------- Accessors
    public function id(): ArticleId { return $this->id; }
    public function headline(): string { return $this->headline; }
    public function content(): string { return $this->content; }
    public function createdAt(): DateTimeImmutable { return $this->createdAt; }
    /** @return list<string> */
    public function imageUuidList(): array { return $this->imageUuidList; }
    /** @return list<MediaAttachment> */
    public function mediaAttachments(): array { return $this->mediaAttachments; }

    // --------- Withers (immutability)
    public function withHeadline(string $headline): self
    {
        $headline = trim($headline);
        if ($headline === '') {
            throw new \DomainException('Headline cannot be empty');
        }
        $clone = clone $this;
        $clone->headline = $headline;
        return $clone;
    }

    public function withContent(string $content): self
    {
        $clone = clone $this;
        $clone->content = $content;
        return $clone;
    }

    /** @param list<string|MediaId> $ids */
    public function withImageUuidList(array $ids): self
    {
        $clone = clone $this;
        $clone->imageUuidList = array_values(array_map(
            fn($v) => $v instanceof MediaId ? $v->toString() : (string) $v,
            $ids
        ));
        return $clone;
    }

    public function addImageUuid(string|MediaId $id): self
    {
        $clone = clone $this;
        $clone->imageUuidList[] = $id instanceof MediaId ? $id->toString() : (string) $id;
        return $clone;
    }

    public function withMediaAttachments(MediaAttachment ...$attachments): self
    {
        $clone = clone $this;
        $clone->mediaAttachments = array_values($attachments);
        return $clone;
    }

    public function addMediaAttachment(MediaAttachment $attachment): self
    {
        $clone = clone $this;
        $clone->mediaAttachments[] = $attachment;
        return $clone;
    }

    // --------- Serialization helpers
    /** @return array<string,mixed> */
    public function jsonSerialize(): array
    {
        return [
            'article_uuid'     => $this->id->toString(),
            'headline'         => $this->headline,
            'content'          => $this->content,
            'created_at'       => $this->createdAt->format(DATE_ATOM),
            'image_uuid_list'  => $this->imageUuidList,
            'media_attachments'=> array_map(fn($a) => $a->jsonSerialize(), $this->mediaAttachments),
        ];
    }

    /** @param array<string,mixed> $row */
    public static function fromArray(array $row): self
    {
        $attachments = array_map(
            fn($a) => MediaAttachment::fromArray($a),
            is_array($row['media_attachments'] ?? null) ? $row['media_attachments'] : []
        );

        return self::reconstitute(
            ArticleId::fromString((string) $row['article_uuid']),
            (string) $row['headline'],
            (string) $row['content'],
            new DateTimeImmutable((string) $row['created_at']),
            is_array($row['image_uuid_list'] ?? null) ? array_values($row['image_uuid_list']) : [],
            $attachments
        );
    }
}
