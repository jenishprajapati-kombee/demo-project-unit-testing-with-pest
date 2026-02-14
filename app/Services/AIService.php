<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Get a solution for a specific error message from an AI provider.
     */
    public function getSolution(string $errorMessage, array $context = []): array
    {
        $apiKey = config('services.ai.api_key');
        $provider = config('services.ai.provider', 'gemini');

        if (!empty($apiKey)) {
            try {
                if ($provider === 'openai') {
                    return $this->queryOpenAI($errorMessage, $context, $apiKey);
                }
                return $this->queryGeminiWithFallback($errorMessage, $context, $apiKey);
            } catch (\Exception $e) {
                Log::warning('AI Service failed, falling back to local logic: ' . $e->getMessage());
                $local = $this->localHeuristicAnalysis($errorMessage, $context);
                $local['ai_error'] = $e->getMessage();
                return $local;
            }
        }

        return $this->localHeuristicAnalysis($errorMessage, $context);
    }

    /**
     * Comprehensive fallback strategy for Gemini.
     */
    private function queryGeminiWithFallback(string $error, array $context, string $apiKey): array
    {
        if (str_starts_with($apiKey, 'sk-')) {
            throw new \Exception('OpenAI key detected! Change AI_PROVIDER to "openai" in .env');
        }

        // Expanded list of model identifiers to try
        $models = [
            'gemini-1.5-flash',
            'gemini-1.5-flash-latest',
            'gemini-pro',
            'gemini-1.0-pro',
        ];

        $lastError = 'No models found';

        foreach ($models as $model) {
            try {
                // Try v1beta first as it's the most common for new features
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

                $response = Http::post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $this->buildPrompt($error, $context)]
                            ]
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $resultText = $response->json('candidates.0.content.parts.0.text');
                    if ($resultText) {
                        return array_merge($this->parseAIResponse($resultText), [
                            'model' => $model,
                            'source' => 'Gemini AI'
                        ]);
                    }
                }

                $errorDetail = $response->json('error.message') ?? "Status " . $response->status();

                // If it's a 403, the key is likely invalid or API not enabled
                if ($response->status() === 403) {
                    throw new \Exception("Gemini API access forbidden (Key invalid or Generative Language API not enabled): " . $errorDetail);
                }

                $lastError = $errorDetail;

                // If not 404, it might be a quota or structural error we should report
                if ($response->status() !== 404) {
                    throw new \Exception("Gemini API Error ({$model}): " . $errorDetail);
                }

            } catch (\Exception $e) {
                if (str_contains($e->getMessage(), 'forbidden') || str_contains($e->getMessage(), 'Gemini API Error')) {
                    throw $e;
                }
                $lastError = $e->getMessage();
                continue;
            }
        }

        throw new \Exception("Gemini models not found or key lacks permissions. Check Google AI Studio. Last error: " . $lastError);
    }

    private function queryOpenAI(string $error, array $context, string $apiKey): array
    {
        $url = "https://api.openai.com/v1/chat/completions";
        $response = Http::withToken($apiKey)->post($url, [
            'model' => 'gpt-3.5-turbo',
            'messages' => [['role' => 'user', 'content' => $this->buildPrompt($error, $context)]],
            'response_format' => ['type' => 'json_object']
        ]);

        if ($response->failed()) {
            throw new \Exception("OpenAI Error: " . ($response->json('error.message') ?? "Status " . $response->status()));
        }

        $resultText = $response->json('choices.0.message.content');
        return array_merge($this->parseAIResponse($resultText), ['source' => 'OpenAI']);
    }

    private function localHeuristicAnalysis(string $error, array $context): array
    {
        $patterns = [
            'Database' => [
                'regex' => '/(PDOException|SQLSTATE|Connection refused|Table.*not found)/i',
                'cause' => 'Database Connection or Schema Issue',
                'steps' => [
                    "Check if MySQL/MariaDB service is running in Laragon.",
                    "Verify DB_DATABASE and credentials in .env.",
                    "Run 'php artisan migrate' to ensure all tables exist."
                ]
            ],
            'Memory' => [
                'regex' => '/(Allowed memory size|Out of memory)/i',
                'cause' => 'PHP Memory Exhaustion',
                'steps' => [
                    "Increase memory_limit in your php.ini.",
                    "Optimize high-memory loops (use cursors/chunks).",
                    "Restart PHP-FPM/Apache."
                ]
            ],
            'Syntax' => [
                'regex' => '/(option.*does not exist|Unknown option|Command.*not found)/i',
                'cause' => 'Artisan Command Error',
                'steps' => [
                    "Verify command spelling and flags.",
                    "Run 'php artisan help <command>' for usage guides."
                ]
            ]
        ];

        foreach ($patterns as $p) {
            if (preg_match($p['regex'], $error)) {
                return [
                    'likely_cause' => $p['cause'],
                    'confidence' => 0.85,
                    'next_steps' => $p['steps'],
                    'reasoning' => 'Detected common Laravel failure pattern via heuristic engine.',
                    'source' => 'Heuristic Engine'
                ];
            }
        }

        return [
            'likely_cause' => 'General Application Error',
            'confidence' => 0.4,
            'next_steps' => ["Review storage/logs/laravel.log for the full stack trace."],
            'reasoning' => 'No specific pattern matched. Fallback to manual review.',
            'source' => 'Heuristic Engine'
        ];
    }

    private function buildPrompt(string $error, array $context): string
    {
        return "You are a Laravel Backend Expert. Analyze this error: \"{$error}\". 
        Context: " . json_encode($context) . ".
        Return a JSON object with: likely_cause (string), confidence (0.0-1.0), next_steps (array of strings), reasoning (string).";
    }

    private function parseAIResponse(?string $text): array
    {
        if (empty($text))
            return ['likely_cause' => 'Empty AI Response', 'confidence' => 0, 'next_steps' => []];
        $cleanJson = preg_replace('/^```json|```$/m', '', trim($text));
        $data = json_decode($cleanJson, true);
        return is_array($data) ? $data : ['likely_cause' => 'Invalid AI JSON', 'confidence' => 0, 'next_steps' => [], 'raw' => $text];
    }
}
