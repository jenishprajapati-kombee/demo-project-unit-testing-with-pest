<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Get a solution for a specific error message from an AI provider.
     * Use Gemini by default as it often has a free tier.
     */
    public function getSolution(string $errorMessage, array $context = []): array
    {
        $apiKey = config('services.ai.api_key');
        $provider = config('services.ai.provider', 'gemini');

        // Attempt to use AI if a key is present
        if (!empty($apiKey)) {
            try {
                if ($provider === 'openai') {
                    return $this->queryOpenAI($errorMessage, $context, $apiKey);
                }
                return $this->queryGemini($errorMessage, $context, $apiKey);
            } catch (\Exception $e) {
                Log::warning('AI Service failed, falling back to local logic: ' . $e->getMessage());
            }
        }

        // Fallback to local heuristic logic (Free & No API required)
        return $this->localHeuristicAnalysis($errorMessage, $context);
    }

    /**
     * Local "Free" analysis logic based on common Laravel failure patterns.
     */
    private function localHeuristicAnalysis(string $error, array $context): array
    {
        $likelyCause = "Unknown System Error";
        $confidence = 0.4;
        $nextSteps = ["Check application logs for full stack trace."];
        $reasoning = "Performed using local pattern matching as AI service is currently unavailable.";

        if (preg_match('/(PDOException|SQLSTATE|Connection refused)/i', $error)) {
            $likelyCause = 'Database Connection Issue';
            $confidence = 0.85;
            $nextSteps = [
                "1. Check if MySQL is running in Laragon/XAMPP.",
                "2. Verify DB_HOST, DB_PORT, and DB_PASSWORD in .env.",
                "3. Run 'php artisan migrate' to ensure tables exist."
            ];
            $reasoning = "Error pattern matches a Database or Connection related exception.";
        } elseif (preg_match('/(Allowed memory size|Out of memory)/i', $error)) {
            $likelyCause = 'PHP Memory Limit Exceeded';
            $confidence = 0.9;
            $nextSteps = [
                "1. Increase 'memory_limit' in your php.ini.",
                "2. Check for infinite loops in your code.",
                "3. Use 'Chunk' for large database processing."
            ];
            $reasoning = "Memory exhaustion detected in the logs.";
        } elseif (preg_match('/(option.*does not exist|Unknown option)/i', $error)) {
            $likelyCause = 'Artisan Command Syntax Error';
            $confidence = 0.95;
            $nextSteps = [
                "1. Check the command spelling.",
                "2. Verify supported flags (e.g. use -v instead of -a).",
                "3. Run 'php artisan help <command>' for usage."
            ];
            $reasoning = "Command line parameter error detected.";
        }

        return [
            'likely_cause' => $likelyCause,
            'confidence' => $confidence,
            'next_steps' => $nextSteps,
            'reasoning' => $reasoning,
            'source' => 'Local Heuristic Engine'
        ];
    }

    private function queryGemini(string $error, array $context, string $apiKey): array
    {
        // Gemini 1.5 Flash API endpoint
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";

        $contextString = json_encode($context);
        $prompt = "You are a professional Laravel Backend Engineer. I have encountered the following error in my application logs:
        
Error: \"{$error}\"

System Context: {$contextString}

Analyze this error and provide:
1. A concise likely cause.
2. A confidence score between 0.0 and 1.0.
3. Step-by-step next steps to fix it.

Respond ONLY in valid JSON format with keys: 'likely_cause', 'confidence', 'next_steps', 'reasoning'.";

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]);

        if ($response->failed()) {
            throw new \Exception('Gemini API request failed: ' . $response->body());
        }

        $resultText = $response->json('candidates.0.content.parts.0.text');
        return $this->parseAIResponse($resultText);
    }

    private function queryOpenAI(string $error, array $context, string $apiKey): array
    {
        $url = "https://api.openai.com/v1/chat/completions";

        $contextString = json_encode($context);
        $prompt = "You are a professional Laravel Backend Engineer. I have encountered the following error in my application logs:
        
Error: \"{$error}\"

System Context: {$contextString}

Analyze this error and provide:
1. A concise likely cause.
2. A confidence score between 0.0 and 1.0.
3. Step-by-step next steps to fix it.

Respond ONLY in valid JSON format with keys: 'likely_cause', 'confidence', 'next_steps', 'reasoning'.";

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withToken($apiKey)->post($url, [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'response_format' => ['type' => 'json_object']
        ]);

        if ($response->failed()) {
            throw new \Exception('OpenAI API request failed: ' . $response->body());
        }

        $resultText = $response->json('choices.0.message.content');
        return $this->parseAIResponse($resultText);
    }

    private function parseAIResponse(?string $text): array
    {
        if (empty($text)) {
            return [
                'likely_cause' => 'AI returned an empty response.',
                'confidence' => 0,
                'next_steps' => 'Try again later.',
            ];
        }

        // Clean up markdown code blocks if the AI included them
        $cleanJson = preg_replace('/```json|```/', '', $text);
        $data = json_decode(trim($cleanJson), true);

        return is_array($data) ? $data : [
            'likely_cause' => 'Failed to parse AI response.',
            'confidence' => 0,
            'next_steps' => 'Check AI output format.',
            'raw_ai_output' => $text
        ];
    }
}
