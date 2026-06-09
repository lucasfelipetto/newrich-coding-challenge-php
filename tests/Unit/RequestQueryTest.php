<?php

declare(strict_types=1);

use App\Http\RequestQuery;

test('applies safe defaults when no params are given', function () {
    $query = RequestQuery::fromArray([]);

    expect($query->status)->toBe(RequestQuery::STATUS_ALL)
        ->and($query->search)->toBe('')
        ->and($query->sort)->toBe(RequestQuery::SORT_NAME)
        ->and($query->direction)->toBe(RequestQuery::DIR_ASC);
});

test('reads and normalizes valid params', function () {
    $query = RequestQuery::fromArray([
        'status' => 'ACTIVE',
        'search' => '  alice  ',
        'sort' => 'Active',
        'dir' => 'DESC',
    ]);

    expect($query->status)->toBe(RequestQuery::STATUS_ACTIVE)
        ->and($query->search)->toBe('alice')
        ->and($query->sort)->toBe(RequestQuery::SORT_ACTIVE)
        ->and($query->direction)->toBe(RequestQuery::DIR_DESC);
});

test('falls back to defaults on invalid values', function () {
    $query = RequestQuery::fromArray([
        'status' => 'banana',
        'sort' => 'unknown',
        'dir' => 'sideways',
    ]);

    expect($query->status)->toBe(RequestQuery::STATUS_ALL)
        ->and($query->sort)->toBe(RequestQuery::SORT_NAME)
        ->and($query->direction)->toBe(RequestQuery::DIR_ASC);
});
