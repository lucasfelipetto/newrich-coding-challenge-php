<?php

declare(strict_types=1);

namespace App\Http;

use App\Model\Item;
use App\Service\ItemService;
use Throwable;

final class ItemController
{
    public function __construct(
        private readonly ItemService $service,
    ) {
    }

    /**
     * @param array<string, mixed> $queryParams
     * @return array{status: int, body: array<string, mixed>}
     */
    public function index(array $queryParams): array
    {
        try {
            $query = RequestQuery::fromArray($queryParams);
            $items = $this->service->getItems($query);

            return [
                'status' => 200,
                'body' => [
                    'data' => array_map(
                        static fn (Item $item): array => $item->toArray(),
                        $items,
                    ),
                ],
            ];
        } catch (Throwable $e) {
            error_log('[items] ' . $e->getMessage());

            return [
                'status' => 502,
                'body' => [
                    'error' => 'Failed to fetch items from the upstream API.',
                ],
            ];
        }
    }
}
