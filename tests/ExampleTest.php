<?php

declare(strict_types=1);

use App\Example;

test('can add two numbers', function () {
    $example = new Example();
    expect($example->add(2, 3))->toBe(5);
});

test('can add negative numbers', function () {
    $example = new Example();
    expect($example->add(-2, 3))->toBe(1);
});
