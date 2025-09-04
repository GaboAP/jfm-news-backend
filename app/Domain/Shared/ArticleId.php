<?php
namespace App\Domain\Shared;

use Ramsey\Uuid\Uuid;

final class ArticleId
{
    private function __construct(private string $value) {}

    public static function new(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromString(string $value): self
    {
        if (!Uuid::isValid($value)) {
            throw new \InvalidArgumentException('Invalid UUID');
        }
        return new self($value);
    }

    public function toString(): string { return $this->value; }
    public function __toString(): string { return $this->value; }
}
