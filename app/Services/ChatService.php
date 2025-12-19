<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Service pour communiquer avec l'API OpenRouter avec support multimodal (images).
 */
class ChatService
{
    public const DEFAULT_MODEL = 'openai/gpt-5-mini';

    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
        $this->baseUrl = rtrim(config('services.openrouter.base_url', 'https://openrouter.ai/api/v1'), '/');
    }

    /**
     * Récupère la liste des modèles disponibles.
     *
     * @return array<int, array{
     *     id: string,
     *     name: string,
     *     description: string,
     *     context_length: int,
     *     max_completion_tokens: int,
     *     input_modalities: array<string>,
     *     output_modalities: array<string>,
     *     supported_parameters: array<string>,
     *     supports_image: bool,
     *     is_free: bool,
     *     pricing: array{prompt: string, completion: string}
     * }>
     */
    public function getModels(): array
    {
        return cache()->remember('openrouter.models', now()->addHour(), function (): array {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/models');

            return collect($response->json('data', []))
                ->sortBy('name')
                ->map(fn (array $model): array => [
                    'id' => $model['id'],
                    'name' => $model['name'],
                    'description' => $model['description'] ?? '',
                    'context_length' => $model['context_length'] ?? 0,
                    'max_completion_tokens' => $model['top_provider']['max_completion_tokens'] ?? 0,
                    'input_modalities' => $model['architecture']['input_modalities'] ?? [],
                    'supports_image' => in_array('image', $model['architecture']['input_modalities'] ?? [], true),
                    'output_modalities' => $model['architecture']['output_modalities'] ?? [],
                    'supported_parameters' => $model['supported_parameters'] ?? [],
                    'is_free' => ($model['pricing']['prompt'] ?? '0') === '0' && ($model['pricing']['completion'] ?? '0') === '0',
                    'pricing' => [
                        'prompt' => $model['pricing']['prompt'] ?? '0',
                        'completion' => $model['pricing']['completion'] ?? '0',
                    ],
                ])
                ->values()
                ->toArray()
            ;
        });
    }

    /**
     * Envoie un message et retourne la réponse du modèle.
     *
     * @param array<int, array{
     *     role: 'assistant'|'system'|'tool'|'user',
     *     content: array<int, array{
     *         type: 'image_url'|'text',
     *         text?: string,
     *         image_url?: array{url: string, detail?: string}
     *     }>|string
     * }> $messages
     */
    public function sendMessage(array $messages, ?string $model = null, float $temperature = 1.0): string
    {
        $model = $model ?? self::DEFAULT_MODEL;
        $messages = [$this->getSystemPrompt(), ...$messages];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'HTTP-Referer' => config('app.url'),
            'X-Title' => config('app.name'),
        ])
            ->timeout(180)
            ->post($this->baseUrl . '/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => $temperature,
            ])
        ;

        // Gestion des erreurs
        if ($response->failed()) {
            $error = $response->json('error.message', 'Erreur inconnue');
            $errorCode = $response->json('error.code', 'N/A');
            $status = $response->status();
            $metadata = $response->json('error.metadata.raw', '');

            \Log::error("OpenRouter API Error for model {$model}", [
                'error' => $error,
                'code' => $errorCode,
                'status' => $status,
                'body' => $response->body(),
            ]);

            // Messages spécifiques selon le type d'erreur
            $userMessage = match(true) {
                // Rate limiting (429)
                $status === 429 && str_contains($metadata, 'rate-limited') =>
                    "Ce modèle est temporairement saturé. Essayez un autre modèle gratuit ou réessayez dans quelques minutes.",

                // Modèle indisponible
                $status === 503 || str_contains($metadata, 'unavailable') || str_contains($metadata, 'offline') =>
                    "Ce modèle est actuellement indisponible. Veuillez choisir un autre modèle.",

                // Modèle déprécié ou retiré
                $status === 404 || str_contains($error, 'not found') || str_contains($error, 'deprecated') =>
                    "Ce modèle n'existe plus ou a été retiré. Veuillez choisir un autre modèle.",

                // Quota dépassé
                str_contains($error, 'quota') || str_contains($error, 'credits') =>
                    "Quota de crédits dépassé. Essayez un modèle gratuit ou attendez le renouvellement.",

                // Contenu bloqué par modération
                $status === 400 && (str_contains($error, 'content') || str_contains($error, 'moderation')) =>
                    "Votre message a été bloqué par le filtre de contenu du modèle.",

                // Erreur générique
                default => "Erreur API ({$model}): {$error}"
            };

            throw new \RuntimeException($userMessage);
        }

        $content = $response->json('choices.0.message.content', '');

        if (empty($content)) {
            \Log::warning("Empty response from model {$model}", [
                'response' => $response->json(),
            ]);
        }

        return $content;
    }

    /**
     * Retourne le prompt système.
     *
     * @return array{role: 'system', content: string}
     */
    private function getSystemPrompt(): array
    {
        $user = auth()->user()?->name ?? 'l\'utilisateur';
        $now = now()->locale('fr')->format('l d F Y H:i');

        return [
            'role' => 'system',
            'content' => view('prompts.system', [
                'now' => $now,
                'user' => $user,
            ])->render(),
        ];
    }
}
