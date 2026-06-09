<?php

declare(strict_types=1);

use App\Http\RequestQuery;
use App\Model\Item;
use App\Service\ItemService;
use Tests\Support\FakeUpstreamApiClient;

/**
 * @param array<int, array<string, mixed>> $items
 */
function makeService(array $items): ItemService
{
    return new ItemService(new FakeUpstreamApiClient($items));
}

/**
 * @param array<int, Item> $items
 * @return array<int, string>
 */
function names(array $items): array
{
    return array_map(static fn (Item $item): string => $item->name, $items);
}

$sampleItems = [
    ['name' => 'Charlie', 'active' => true],
    ['name' => 'alice', 'active' => false],
    ['name' => 'Bob', 'active' => true],
    ['name' => 'Diana', 'active' => false],
];

test('maps raw payload into Item objects', function () use ($sampleItems) {
    $items = makeService($sampleItems)->getItems(RequestQuery::fromArray([]));

    expect($items)->toHaveCount(4)
        ->and($items[0])->toBeInstanceOf(Item::class);
});

test('filters by active status', function () use ($sampleItems) {
    $items = makeService($sampleItems)->getItems(
        RequestQuery::fromArray(['status' => 'active']),
    );

    expect(names($items))->toEqualCanonicalizing(['Charlie', 'Bob'])
        ->and($items)->each->toHaveProperty('active', true);
});

test('filters by inactive status', function () use ($sampleItems) {
    $items = makeService($sampleItems)->getItems(
        RequestQuery::fromArray(['status' => 'inactive']),
    );

    expect(names($items))->toEqualCanonicalizing(['alice', 'Diana']);
});

test('search is case-insensitive and matches substrings', function () use ($sampleItems) {
    $items = makeService($sampleItems)->getItems(
        RequestQuery::fromArray(['search' => 'A']),
    );

    expect(names($items))->toEqualCanonicalizing(['Charlie', 'alice', 'Diana']);
});

test('combines status filter and search', function () use ($sampleItems) {
    $items = makeService($sampleItems)->getItems(
        RequestQuery::fromArray(['status' => 'active', 'search' => 'b']),
    );

    expect(names($items))->toBe(['Bob']);
});

test('sorts by name ascending and is case-insensitive', function () use ($sampleItems) {
    $items = makeService($sampleItems)->getItems(
        RequestQuery::fromArray(['sort' => 'name', 'dir' => 'asc']),
    );

    expect(names($items))->toBe(['alice', 'Bob', 'Charlie', 'Diana']);
});

test('sorts by name descending', function () use ($sampleItems) {
    $items = makeService($sampleItems)->getItems(
        RequestQuery::fromArray(['sort' => 'name', 'dir' => 'desc']),
    );

    expect(names($items))->toBe(['Diana', 'Charlie', 'Bob', 'alice']);
});

test('sorts by active status', function () use ($sampleItems) {
    $items = makeService($sampleItems)->getItems(
        RequestQuery::fromArray(['sort' => 'active', 'dir' => 'desc']),
    );

    // active (true) first when descending
    expect($items[0]->active)->toBeTrue()
        ->and($items[3]->active)->toBeFalse();
});

test('returns reindexed array after filtering', function () use ($sampleItems) {
    $items = makeService($sampleItems)->getItems(
        RequestQuery::fromArray(['status' => 'active']),
    );

    expect(array_keys($items))->toBe([0, 1]);
});

test('returns an empty list when upstream has no items', function () {
    $items = makeService([])->getItems(RequestQuery::fromArray([]));

    expect($items)->toBe([]);
});

test('propagates upstream failures', function () {
    $client = new FakeUpstreamApiClient([], new RuntimeException('boom'));
    $service = new ItemService($client);

    expect(fn () => $service->getItems(RequestQuery::fromArray([])))
        ->toThrow(RuntimeException::class, 'boom');
});
