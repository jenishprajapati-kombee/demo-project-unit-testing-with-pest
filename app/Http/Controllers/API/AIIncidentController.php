<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnalyzeIncidentRequest;
use App\Services\AIIncidentAnalyzerService;
use Illuminate\Http\JsonResponse;

class AIIncidentController extends Controller
{
    protected $analyzerService;

    public function __construct(AIIncidentAnalyzerService $analyzerService)
    {
        $this->analyzerService = $analyzerService;
    }

    /**
     * Task 1: Log Intake API
     * Accept Logs + Metrics and trigger AI Analysis.
     */
    public function analyze(AnalyzeIncidentRequest $request): JsonResponse
    {
        try {
            $incident = $this->analyzerService->analyzeIncident(
                $request->input('logs'),
                $request->input('metrics')
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'likely_cause' => $incident->likely_cause,
                    'confidence' => $incident->confidence,
                    'reasoning' => $incident->reasoning,
                    'next_steps' => $incident->next_steps,
                    'severity' => $incident->severity,
                    'incident_id' => $incident->id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Analysis failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
