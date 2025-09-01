<?php

namespace App\Domain\Shared;

final class Url
{
    private function __construct(private string $value) {}

    public static function fromString(string $value): self
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid URL');
        }
        return new self($value);
    }

    public function toString(): string { return $this->value; }
    public function __toString(): string { return $this->value; }
}
