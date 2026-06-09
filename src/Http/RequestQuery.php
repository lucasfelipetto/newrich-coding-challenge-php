<?php

declare(strict_types=1);

namespace App\Http;

final class RequestQuery
{
    public const STATUS_ALL = 'all';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public const SORT_NAME = 'name';
    public const SORT_ACTIVE = 'active';

    public const DIR_ASC = 'asc';
    public const DIR_DESC = 'desc';

    public function __construct(
        public readonly string $status = self::STATUS_ALL,
        public readonly string $search = '',
        public readonly string $sort = self::SORT_NAME,
        public readonly string $direction = self::DIR_ASC,
    ) {
    }

    /**
     * @param array<string, mixed> $params
     */
    public static function fromArray(array $params): self
    {
        return new self(
            status: self::pick(
                $params['status'] ?? null,
                [self::STATUS_ALL, self::STATUS_ACTIVE, self::STATUS_INACTIVE],
                self::STATUS_ALL,
            ),
            search: trim((string) ($params['search'] ?? '')),
            sort: self::pick(
                $params['sort'] ?? null,
                [self::SORT_NAME, self::SORT_ACTIVE],
                self::SORT_NAME,
            ),
            direction: self::pick(
                $params['dir'] ?? null,
                [self::DIR_ASC, self::DIR_DESC],
                self::DIR_ASC,
            ),
        );
    }

    /**
     * @param array<int, string> $allowed
     */
    private static function pick(mixed $value, array $allowed, string $default): string
    {
        $normalized = strtolower(trim((string) ($value ?? '')));

        return in_array($normalized, $allowed, true) ? $normalized : $default;
    }
}
