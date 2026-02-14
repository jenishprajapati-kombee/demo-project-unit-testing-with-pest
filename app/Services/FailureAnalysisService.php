<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class FailureAnalysisService
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Perform analysis of the log file to identify errors.
     *
     * @return array
     */
    public function analyze(): array
    {
        $logAnalysis = $this->analyzeLogs();

        // If an error was detected, ask AI for a solution
        $aiSolution = null;
        if (!empty($logAnalysis['recent_errors'])) {
            $latestError = $logAnalysis['recent_errors'][count($logAnalysis['recent_errors']) - 1]['message'];
            $aiSolution = $this->aiService->getSolution($latestError, [
                'logs' => $logAnalysis['recent_errors']
            ]);
        }

        return [
            'timestamp' => now()->toDateTimeString(),
            'status' => !empty($logAnalysis['recent_errors']) ? 'error' : 'healthy',
            'errors_detected' => $logAnalysis['recent_errors'],
            'ai_recommendation' => $aiSolution,
        ];
    }

    /**
     * Analyze Laravel log files for recent errors and exceptions.
     */
    private function analyzeLogs(): array
    {
        $logPath = storage_path('logs/laravel.log');
        if (!File::exists($logPath)) {
            return [
                'error' => 'Log file not found',
                'recent_errors' => [],
                'error_count' => 0
            ];
        }

        try {
            $content = File::get($logPath);
            $allLines = explode("\n", str_replace("\r", "", $content));
            $lines = array_slice($allLines, -500);
        } catch (\Exception $e) {
            return [
                'error' => 'Failed to read log file: ' . $e->getMessage(),
                'recent_errors' => [],
                'error_count' => 0
            ];
        }

        $errors = [];
        $errorPatterns = [
            'Database' => '/(PDOException|QueryException|SQLSTATE)/i',
            'Memory' => '/(Allowed memory size|Out of memory)/i',
            'Timeout' => '/(Maximum execution time|Request timeout)/i',
            'Authentication' => '/(Unauthorized|Unauthenticated)/i',
            'FilePermissions' => '/(Permission denied|failed to open stream)/i',
            'SystemEmergency' => '/(\.EMERGENCY|\.CRITICAL|\.ALERT)/i',
            'ApplicationError' => '/(\.ERROR|exception|error:)/i',
        ];

        foreach ($lines as $line) {
            if (empty(trim($line)))
                continue;

            if (preg_match('/\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}\]/', $line)) {
                foreach ($errorPatterns as $type => $pattern) {
                    if (preg_match($pattern, $line)) {
                        $errors[] = [
                            'type' => $type,
                            'message' => trim($line),
                        ];
                        break;
                    }
                }
            }
        }

        return [
            'error_count' => count($errors),
            'recent_errors' => array_values(array_unique($errors, SORT_REGULAR)),
        ];
    }
}
