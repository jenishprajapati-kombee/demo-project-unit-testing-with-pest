<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\FailureAnalysisService;
use Illuminate\Http\JsonResponse;

class FailureAnalysisAPIController extends Controller
{
    protected $analysisService;

    public function __construct(FailureAnalysisService $analysisService)
    {
        $this->analysisService = $analysisService;
    }

    /**
     * Get a comprehensive analysis of the system status and recent failures.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $analysis = $this->analysisService->analyze();

            return response()->json([
                'success' => true,
                'message' => 'System analysis completed successfully.',
                'data' => $analysis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform system analysis.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
