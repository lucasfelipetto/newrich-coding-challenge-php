<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Client\UpstreamApiClientInterface;
use RuntimeException;

final class FakeUpstreamApiClient implements UpstreamApiClientInterface
{
    /**
     * @param array<int, array<string, mixed>> $items
     */
    public function __construct(
        private readonly array $items = [],
        private readonly ?RuntimeException $error = null,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function fetchItems(): array
    {
        if ($this->error !== null) {
            throw $this->error;
        }

        return $this->items;
    }
}
