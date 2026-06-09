<?php

declare(strict_types=1);

use App\Model\Item;

test('builds an item from an array', function () {
    $item = Item::fromArray(['name' => 'Alice', 'active' => true]);

    expect($item->name)->toBe('Alice')
        ->and($item->active)->toBeTrue();
});

test('falls back to safe defaults when keys are missing', function () {
    $item = Item::fromArray([]);

    expect($item->name)->toBe('')
        ->and($item->active)->toBeFalse();
});

test('coerces non-string name and truthy active', function () {
    $item = Item::fromArray(['name' => 123, 'active' => 1]);

    expect($item->name)->toBe('123')
        ->and($item->active)->toBeTrue();
});

test('serializes back to an array', function () {
    $item = new Item('Bob', false);

    expect($item->toArray())->toBe(['name' => 'Bob', 'active' => false]);
});
