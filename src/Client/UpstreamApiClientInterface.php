<?php

declare(strict_types=1);

namespace App\Client;

interface UpstreamApiClientInterface
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function fetchItems(): array;
}
