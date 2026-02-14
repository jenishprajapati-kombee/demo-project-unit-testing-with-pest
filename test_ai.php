<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    Illuminate\Http\Request::capture()
);

$analysisService = app(App\Services\FailureAnalysisService::class);
$result = $analysisService->analyze();

echo "=== AI ANALYSIS RESULT ===\n";
echo json_encode($result['ai_recommendation'] ?? $result['ai_analysis'], JSON_PRETTY_PRINT);
echo "\n==========================\n";
