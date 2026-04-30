<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

$data = [
    [
        'name' => 'Alice',
        'active' => true,
    ],
    [
        'name' => 'Bob',
        'active' => false,
    ],
    [
        'name' => 'Charlie',
        'active' => true,
    ],
    [
        'name' => 'Diana',
        'active' => true,
    ],
    [
        'name' => 'Eve',
        'active' => false,
    ],
];

echo json_encode($data);
