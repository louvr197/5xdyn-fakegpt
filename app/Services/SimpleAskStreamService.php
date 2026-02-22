<?php

declare(strict_types=1);

namespace App\Services;

use Generator;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\StreamInterface;

class SimpleAskStreamService
{
    public const DEFAULT_MODEL = 'openai/gpt-4o-mini';

    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
        $this->baseUrl = rtrim(config('services.openrouter.base_url', 'https://openrouter.ai/api/v1'), '/');
    }

    /**
     * Récupère la liste légère des modèles.
     */
    // public function getModels(): array
    // {
    //     return cache()->remember('openrouter.models', now()->addHour(), function (): array {
    //         $response = Http::withToken($this->apiKey)->get("{$this->baseUrl}/models");

    //         return collect($response->json('data', []))
    //             ->sortBy('name')
    //             ->map(fn(array $m): array => ['id' => $m['id'], 'name' => $m['name']])
    //             ->values()
    //             ->toArray();
    //     });
    // }
    public function getModels(): array
    {
        return cache()->remember('openrouter.models', now()->addHour(), function (): array {
            $response = Http::withToken($this->apiKey)->get("{$this->baseUrl}/models");

            // DEBUG
            if ($response->failed()) {
                \Log::error('Failed to fetch models', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return []; // Retourne tableau vide si erreur
            }

            $models = collect($response->json('data', []))
                ->sortBy('name')
                ->map(fn(array $m): array => ['id' => $m['id'], 'name' => $m['name']])
                ->values()
                ->toArray();

            \Log::info('Models fetched', ['count' => count($models)]);
            return $models;
        });
    }

    /**
     * Stream un message en temps réel vers la sortie.
     */
    public function streamToOutput(
        array $messages,
        ?string $model = null,
        float $temperature = 1.0,
        ?string $reasoningEffort = null
    ): void {
        $response = $this->sendStreamRequest($messages, $model, $temperature, $reasoningEffort);

        if ($response->failed()) {
            $errorMsg = $response->json('error.message', 'HTTP Error');
            $errorCode = $response->status();
            $fullBody = $response->body();

            echo "[ERROR] Code {$errorCode}: {$errorMsg}\n\nFull response: {$fullBody}";
            $this->flush();
            return;
        }

        foreach ($this->parseSSEStream($response->toPsrResponse()->getBody()) as $event) {
            if ($event['type'] === 'error') {
                echo "[ERROR] " . $event['data'];
                $this->flush();
                return;
            }

            if ($event['type'] === 'content' && $event['data']) {
                echo $event['data'];
                $this->flush();
            }

            if ($event['type'] === 'reasoning' && $event['data']) {
                echo "[REASONING]" . $event['data'] . "[/REASONING]";
                $this->flush();
            }
        }
    }

    private function flush(): void
    {
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
    }

    private function sendStreamRequest(
        array $messages,
        ?string $model,
        float $temperature,
        ?string $reasoningEffort
    ): \Illuminate\Http\Client\Response {
        $payload = [
            'model' => $model ?? self::DEFAULT_MODEL,
            'messages' => [$this->getSystemPrompt(), ...$messages],
            'temperature' => $temperature,
            'stream' => true,
        ];

        if ($reasoningEffort !== null) {
            $payload['reasoning'] = ['effort' => $reasoningEffort];
        }

        return Http::withToken($this->apiKey)
            ->withHeaders([
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])
            ->withOptions(['stream' => true])
            ->timeout(120)
            ->post("{$this->baseUrl}/chat/completions", $payload);
    }

    private function parseSSEStream(StreamInterface $body): Generator
    {
        $buffer = '';

        while (!$body->eof()) {
            $buffer .= $body->read(1024);

            while (($pos = strpos($buffer, "\n")) !== false) {
                $line = trim(substr($buffer, 0, $pos));
                $buffer = substr($buffer, $pos + 1);

                if ($event = $this->parseSSELine($line)) {
                    yield $event;
                }
            }
        }
    }

    private function parseSSELine(string $line): ?array
    {
        if ($line === '' || str_starts_with($line, ':')) {
            return null;
        }

        if (!str_starts_with($line, 'data: ')) {
            return null;
        }

        $data = substr($line, 6);

        if ($data === '[DONE]') {
            return ['type' => 'done', 'data' => null];
        }

        return $this->parseJSON($data);
    }

    private function parseJSON(string $json): ?array
    {
        try {
            $parsed = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

            if (isset($parsed['error'])) {
                return ['type' => 'error', 'data' => $parsed['error']['message'] ?? 'Unknown error'];
            }

            $delta = $parsed['choices'][0]['delta'] ?? [];

            if (!empty($delta['content'])) {
                return ['type' => 'content', 'data' => $delta['content']];
            }

            if (!empty($delta['reasoning']) || !empty($delta['reasoning_content'])) {
                return ['type' => 'reasoning', 'data' => $delta['reasoning'] ?? $delta['reasoning_content']];
            }

            return null;
        } catch (\JsonException) {
            return null;
        }
    }

    /**
     * Retourne le prompt système.
     */
    private function getSystemPrompt(): array
    {
        $user = auth()->user();
        $userName = $user?->name ?? 'l\'utilisateur';
        $now = now()->locale('fr')->format('l d F Y H:i');

        $customInstructions = $this->buildCustomInstructions($user);

        return [
            'role' => 'system',
            'content' => view('prompts.system', [
                'user' => $userName,
                'now' => $now,
                'customInstructions' => $customInstructions,
            ])->render(),
        ];
    }

    /**
     * Construit les instructions personnalisées.
     */
    private function buildCustomInstructions($user): string
    {
        if (!$user) {
            return '';
        }

        $parts = [];

        if (!empty($user->custom_instructions_about)) {
            $parts[] = "À propos de l'utilisateur :\n" . $user->custom_instructions_about;
        }

        if (!empty($user->custom_instructions_behavior)) {
            $parts[] = "Comportement attendu :\n" . $user->custom_instructions_behavior;
        }

        if (!empty($user->custom_instructions_commands)) {
            $parts[] = "Commandes personnalisées :\n" . $user->custom_instructions_commands;
        }

        return !empty($parts) ? "\n\n" . implode("\n\n", $parts) : '';
    }
}
