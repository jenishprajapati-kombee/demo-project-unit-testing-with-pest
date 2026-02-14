<?php

namespace App\Services;

use App\Models\Incident;
use Illuminate\Support\Facades\Log;

class AIIncidentAnalyzerService
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Process and analyze an incident report.
     */
    public function analyzeIncident(array $logs, array $metrics): Incident
    {
        // 1. Task 2: Preprocessing
        $preprocessedData = $this->preprocess($logs, $metrics);

        // 2. Task 3: Real AI Analysis
        $aiAnalysis = $this->aiService->getSolution(implode("\n", $logs), [
            'metrics' => $metrics,
            'severity' => $preprocessedData['severity']
        ]);

        // 3. Task 4: Decision Layer (validating or refining AI suggestions)
        $decision = $this->decisionLayer($aiAnalysis, $preprocessedData['metrics']);

        // 4. Task 5: Store and Return
        return Incident::create([
            'raw_logs' => $logs,
            'raw_metrics' => $metrics,
            'severity' => $preprocessedData['severity'],
            'likely_cause' => $decision['likely_cause'],
            'confidence' => $decision['confidence'],
            'reasoning' => $decision['reasoning'],
            'next_steps' => $decision['next_steps'],
        ]);
    }

    /**
     * Preprocessing: Remove duplicates, group and mark severity.
     */
    private function preprocess(array $logs, array $metrics): array
    {
        // Remove duplicate log messages
        $uniqueLogs = array_values(array_unique($logs));

        // Basic severity marking
        $severity = 'low';
        $criticalKeywords = ['timeout', 'connection reset', 'critical', 'fatal', 'down'];

        foreach ($uniqueLogs as $log) {
            foreach ($criticalKeywords as $keyword) {
                if (stripos($log, $keyword) !== false) {
                    $severity = 'high';
                    break 2;
                }
            }
        }

        if (($metrics['cpu'] ?? 0) > 90 || ($metrics['db_latency'] ?? 0) > 500) {
            $severity = 'high';
        }

        return [
            'logs' => $uniqueLogs,
            'metrics' => $metrics,
            'severity' => $severity
        ];
    }

    /**
     * Decision Layer: Refine the AI's output based on hard metrics.
     */
    private function decisionLayer(array $aiOutput, array $metrics): array
    {
        // If CPU is extreme, override confidence for resource issues
        if (($metrics['cpu'] ?? 0) > 95 && $aiOutput['likely_cause'] === 'High Resource Usage') {
            $aiOutput['confidence'] = 0.95;
            $aiOutput['reasoning'] .= " Elevated to High Confidence due to extreme CPU saturation.";
        }

        // If multiple signals point to DB, solidify finding
        if ($aiOutput['likely_cause'] === 'Database Overload' && ($metrics['requests_per_sec'] ?? 'Low') === 'High') {
            $aiOutput['confidence'] = 0.90;
            $aiOutput['reasoning'] .= " High request volume combined with DB latency confirms overload.";
        }

        return $aiOutput;
    }

    private function getRecommendations(string $cause): string
    {
        return match ($cause) {
            'Database Overload' => "1. Check DB connection pool. 2. Scaling DB instance. 3. Check for long-running queries.",
            'Network Instability' => "1. Check VPC routes. 2. Verify DB security groups. 3. Ping DB host from App.",
            'High Resource Usage' => "1. Identify expensive endpoints. 2. Increase horizontal scaling. 3. Check for memory leaks.",
            default => "1. Inspect full stack traces. 2. Check recent deployments. 3. Monitor cloud health dashboard."
        };
    }
}
