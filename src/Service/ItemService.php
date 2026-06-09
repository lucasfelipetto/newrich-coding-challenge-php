<?php

declare(strict_types=1);

namespace App\Service;

use App\Client\UpstreamApiClientInterface;
use App\Http\RequestQuery;
use App\Model\Item;

final class ItemService
{
    public function __construct(
        private readonly UpstreamApiClientInterface $client,
    ) {
    }

    /**
     * @return array<int, Item>
     */
    public function getItems(RequestQuery $query): array
    {
        $items = array_map(
            static fn (array $raw): Item => Item::fromArray($raw),
            $this->client->fetchItems(),
        );

        $items = $this->filter($items, $query);
        $items = $this->sort($items, $query);

        return array_values($items);
    }

    /**
     * @param array<int, Item> $items
     * @return array<int, Item>
     */
    private function filter(array $items, RequestQuery $query): array
    {
        return array_filter($items, static function (Item $item) use ($query): bool {
            $matchesStatus = match ($query->status) {
                RequestQuery::STATUS_ACTIVE => $item->active,
                RequestQuery::STATUS_INACTIVE => !$item->active,
                default => true,
            };

            if (!$matchesStatus) {
                return false;
            }

            if ($query->search === '') {
                return true;
            }

            return str_contains(
                mb_strtolower($item->name),
                mb_strtolower($query->search),
            );
        });
    }

    /**
     * @param array<int, Item> $items
     * @return array<int, Item>
     */
    private function sort(array $items, RequestQuery $query): array
    {
        usort($items, static function (Item $a, Item $b) use ($query): int {
            $comparison = match ($query->sort) {
                RequestQuery::SORT_ACTIVE => (int) $a->active <=> (int) $b->active,
                default => strcasecmp($a->name, $b->name),
            };

            return $query->direction === RequestQuery::DIR_DESC ? -$comparison : $comparison;
        });

        return $items;
    }
}
