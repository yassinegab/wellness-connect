<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class QwenService
{
    private string $apiKey;
    private HttpClientInterface $client;

    public function __construct(
        HttpClientInterface $client,
        #[Autowire(env: 'OPENROUTER_API_KEY')] string $apiKey = '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' 
    ) {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    public function analyzeMeal(string $imagePath, ?string $description): string
    {
        $imageData = base64_encode(file_get_contents($imagePath));
        $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
        $mimeType = match($extension) {
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };

        $userPrompt = "Analyze this meal photo. ";
        if ($description) {
            $userPrompt .= "User description: " . $description . ". ";
        }
        $userPrompt .= "Provide nutritional insights (calories, macros estimation) and health recommendations. Keep it concise and encouraging.";

        try {
            $response = $this->client->request('POST', 'https://openrouter.ai/api/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => 'https://localhost', // Required by OpenRouter
                    'X-Title' => 'HealthCare App', // Required by OpenRouter
                ],
                'json' => [
                    'model' => 'qwen/qwen-2.5-vl-72b-instruct',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => $userPrompt
                                ],
                                [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => "data:$mimeType;base64,$imageData"
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            $content = $response->toArray();
            return $content['choices'][0]['message']['content'] ?? 'No analysis available.';

        } catch (\Exception $e) {
            return "AI Analysis failed: " . $e->getMessage();
        }
    }

    public function analyzeText(string $prompt): string
    {
        try {
            $response = $this->client->request('POST', 'https://openrouter.ai/api/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'HTTP-Referer' => 'https://localhost',
                    'X-Title' => 'HealthCare App',
                ],
                'json' => [
                    'model' => 'qwen/qwen-2.5-vl-72b-instruct',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ],
                    ],
                ],
            ]);

            $data = $response->toArray();
            return $data['choices'][0]['message']['content'] ?? 'Analysis failed.';

        } catch (\Exception $e) {
            return 'AI Recommendation Error: ' . $e->getMessage();
        }
    }
}
