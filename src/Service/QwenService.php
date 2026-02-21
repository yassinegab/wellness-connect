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
        #[Autowire(env: 'OPENROUTER_API_KEY')] string $apiKey
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
        $userPrompt .= "Provide nutritional insights and health recommendations. 
        You MUST respond in JSON format with the following keys:
        - calories: estimated calories (number)
        - sugar: estimated sugar in grams (number)
        - protein: estimated protein in grams (number)
        - analysis: a concise and encouraging textual analysis
        - stress_link: a brief insight on how this meal might affect stress or restlessness (e.g., 'High sugar might increase restlessness').
        
        Keep it professional and empathetic.";

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

        } catch (\Symfony\Component\HttpClient\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode === 401 || $statusCode === 403) {
                return json_encode(['analysis' => "AI Analysis failed: Authentication error. Please check your OPENROUTER_API_KEY."]);
            }
            return json_encode(['analysis' => "AI Analysis failed: " . $e->getMessage()]);
        } catch (\Exception $e) {
            return json_encode(['analysis' => "AI Analysis failed: " . $e->getMessage()]);
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

        } catch (\Symfony\Component\HttpClient\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode === 401 || $statusCode === 403) {
                return "AI Recommendation Error: Authentication error. Please check your OPENROUTER_API_KEY.";
            }
            return 'AI Recommendation Error: ' . $e->getMessage();
        } catch (\Exception $e) {
            return 'AI Recommendation Error: ' . $e->getMessage();
        }
    }

    public function getChatCompletion(array $messages): string
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
                    'messages' => $messages,
                ],
            ]);

            $data = $response->toArray();
            return $data['choices'][0]['message']['content'] ?? 'Response failed.';

        } catch (\Symfony\Component\HttpClient\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode === 401 || $statusCode === 403) {
                return "AI Error: Authentication error. Please check your OPENROUTER_API_KEY.";
            }
            return 'AI Error: ' . $e->getMessage();
        } catch (\Exception $e) {
            return 'AI Error: ' . $e->getMessage();
        }
    }
}
