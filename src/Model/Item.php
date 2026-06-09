<?php

declare(strict_types=1);

namespace App\Model;

final class Item
{
    public function __construct(
        public readonly string $name,
        public readonly bool $active,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) ($data['name'] ?? ''),
            active: (bool) ($data['active'] ?? false),
        );
    }

    /**
     * @return array{name: string, active: bool}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'active' => $this->active,
        ];
    }
}
