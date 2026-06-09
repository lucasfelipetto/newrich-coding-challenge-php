<?php

declare(strict_types=1);

use App\Client\UpstreamApiClient;
use App\Http\ItemController;
use App\Service\ItemService;

require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$upstreamUrl = getenv('UPSTREAM_API_URL') ?: 'http://localhost:8000';

$controller = new ItemController(
    new ItemService(
        new UpstreamApiClient($upstreamUrl),
    ),
);

$response = $controller->index($_GET);

http_response_code($response['status']);
echo json_encode(
    $response['body'],
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
);
