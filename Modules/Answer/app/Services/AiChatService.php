<?php

namespace Modules\Answer\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatService
{
    public function sendMessage($userMessage, $model)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
                'Content-Type'  => 'application/json',
                'HTTP-Referer' => env('APP_URL'), // Required by OpenRouter
                'X-Title'      => env('APP_NAME', 'Laravel App'), // Required by OpenRouter
            ])->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'user', 'content' => $userMessage],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Log::debug('AI API Response:', $data);
                
                if (isset($data['choices'][0]['message']['content'])) {
                    return $data['choices'][0]['message']['content'];
                } else {
                    Log::error('AI Chat API Success, but unexpected response format', [
                        'response' => $data,
                        'model' => $model,
                        'message' => $userMessage
                    ]);
                    return 'No valid response from AI. Response format unexpected.';
                }
            } else {
                Log::error('AI Chat API Error:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'model' => $model,
                    'message' => $userMessage
                ]);
                return 'AI request failed with status: ' . $response->status();
            }
        } catch (\Exception $e) {
            Log::error('AI Chat Service Exception: ' . $e->getMessage());
            return 'AI service unavailable: ' . $e->getMessage();
        }
    }
}
