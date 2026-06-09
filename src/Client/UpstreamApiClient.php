<?php

declare(strict_types=1);

namespace App\Client;

use RuntimeException;

final class UpstreamApiClient implements UpstreamApiClientInterface
{
    public function __construct(
        private readonly string $baseUrl = 'http://localhost:8000',
        private readonly int $timeout = 5,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function fetchItems(): array
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        unset($ch);

        if ($response === false || $error !== '') {
            throw new RuntimeException('Failed to reach upstream API: ' . $error);
        }

        if ($status >= 400) {
            throw new RuntimeException('Upstream API returned HTTP status ' . $status);
        }

        $decoded = json_decode((string) $response, true);

        if (!is_array($decoded)) {
            throw new RuntimeException('Failed to decode upstream API response as JSON.');
        }

        return $decoded;
    }
}
