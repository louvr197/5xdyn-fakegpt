<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Services\ChatService;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;

class MessageController extends Controller
{
    public function __construct(private ChatService $chatService, private ImageService $imageService) {}

    /**
     * Store a new message and get AI response.
     */
    public function store(Request $request, Conversation $conversation)
    {
        // Vérifier que la conversation appartient à l'utilisateur
        Gate::authorize('view', $conversation);

        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|max:5120',
        ]);

        // Préparer le contenu (texte + image si présente)
        $userContent = $request->content;
        $imageUrl = null;

        if ($request->hasFile('image')) {
            // Uploader l'image
            $imageUrl = $this->imageService->uploadImage($request->file('image'));

            // Convertir en base64 pour l'API
            $imageBase64 = $this->imageService->imageToBase64($request->file('image'));

            // Format multimodal pour OpenRouter
            $userContent = [
                ['type' => 'text', 'text' => $request->content],
                ['type' => 'image_url', 'image_url' => ['url' => $imageBase64]]
            ];
        }

        // Sauvegarder le message de l'utilisateur
        $userMessage = $conversation->messages()->create([
            'role' => 'user',
            'content' => is_array($userContent) ? json_encode($userContent) : $userContent,
        ]);

        try {
            // Récupérer tous les messages de la conversation pour le contexte
            $messages = $conversation->messages()
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(fn($msg) => [
                    'role' => $msg->role,
                    'content' => json_decode($msg->content, true) ?? $msg->content,
                ])
                ->toArray();

            // Appeler l'API
            $response = $this->chatService->sendMessage(
                messages: $messages,
                model: $conversation->model
            );

            // Sauvegarder la réponse de l'assistant
            $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $response,
            ]);

            // Si le modèle réussit, le retirer des modèles en échec
            \App\Models\FailedModel::where('model_id', $conversation->model)->delete();

            // Générer le titre si c'est le premier message
            if ($conversation->messages()->count() === 2 && !$conversation->title) {
                $this->generateTitle($conversation, $messages, $response);
            }

            // Mettre à jour le timestamp de la conversation
            $conversation->touch();
        } catch (\Exception $e) {
            // Enregistrer le modèle comme ayant échoué (partagé entre tous les utilisateurs)
            \App\Models\FailedModel::updateOrCreate(
                ['model_id' => $conversation->model],
                [
                    'last_error' => $e->getMessage(),
                    'last_failed_at' => now(),
                ]
            )->increment('failure_count');

            // Sauvegarder le message d'erreur comme réponse de l'assistant
            $conversation->messages()->create([
                'role' => 'assistant',
                'content' => '⚠️ **Erreur** : ' . $e->getMessage(),
            ]);

            $conversation->touch();
        }

        return back();
    }

    /**
     * Stream a message response in real-time.
     */
    public function stream(Request $request, Conversation $conversation)
    {
        Gate::authorize('view', $conversation);

        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|max:5120',
        ]);

        // Préparer le contenu
        $userContent = $request->content;
        $imageUrl = null;

        if ($request->hasFile('image')) {
            $imageUrl = $this->imageService->uploadImage($request->file('image'));
            $imageBase64 = $this->imageService->imageToBase64($request->file('image'));
            $userContent = [
                ['type' => 'text', 'text' => $request->content],
                ['type' => 'image_url', 'image_url' => ['url' => $imageBase64]]
            ];
        }

        // Sauvegarder le message utilisateur
        $userMessage = $conversation->messages()->create([
            'role' => 'user',
            'content' => is_array($userContent) ? json_encode($userContent) : $userContent,
        ]);

        // Récupérer l'historique
        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($msg) => [
                'role' => $msg->role,
                'content' => json_decode($msg->content, true) ?? $msg->content,
            ])
            ->toArray();

        // Ajouter le prompt système
        $systemPrompt = $this->getSystemPrompt($conversation);
        array_unshift($messages, $systemPrompt);

        // Désactiver le buffering
        @ob_end_clean();
        ob_implicit_flush(true);
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', '1');
        }
        @ini_set('zlib.output_compression', '0');
        @ini_set('output_buffering', 'off');
        set_time_limit(180);

        return response()->stream(function () use ($conversation, $messages) {
            // Envoyer un ping initial pour établir la connexion
            echo ": ping\n\n";
            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();

            try {
                $fullResponse = '';

                foreach ($this->streamFromAPI($messages, $conversation->model) as $chunk) {
                    if ($chunk['type'] === 'content' && $chunk['data']) {
                        $fullResponse .= $chunk['data'];
                        echo "data: " . json_encode(['content' => $chunk['data']]) . "\n\n";

                        // Forcer le flush immédiatement
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }

                    if ($chunk['type'] === 'error') {
                        echo "data: " . json_encode(['error' => $chunk['data']]) . "\n\n";

                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                        return;
                    }
                }

                // Sauvegarder la réponse complète
                $conversation->messages()->create([
                    'role' => 'assistant',
                    'content' => $fullResponse,
                ]);

                // Retirer des modèles en échec si succès
                \App\Models\FailedModel::where('model_id', $conversation->model)->delete();

                // Rafraîchir et générer le titre si premier message
                $conversation->refresh();
                $messageCount = $conversation->messages()->count();

                // Générer le titre si c'est le premier message et pas de titre existant
                if ($messageCount === 2 && empty($conversation->title)) {
                    try {
                        $this->generateTitle($conversation, $messages, $fullResponse);
                    } catch (\Exception $e) {
                        \Log::warning("Failed to generate title: " . $e->getMessage());
                        // Définir un titre par défaut si la génération échoue
                        $conversation->update(['title' => 'Nouvelle conversation']);
                    }
                }

                $conversation->touch();

                echo "data: " . json_encode(['done' => true]) . "\n\n";
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();

            } catch (\Exception $e) {
                \App\Models\FailedModel::updateOrCreate(
                    ['model_id' => $conversation->model],
                    [
                        'last_error' => $e->getMessage(),
                        'last_failed_at' => now(),
                    ]
                )->increment('failure_count');

                $conversation->messages()->create([
                    'role' => 'assistant',
                    'content' => '⚠️ **Erreur** : ' . $e->getMessage(),
                ]);

                echo "data: " . json_encode(['error' => $e->getMessage()]) . "\n\n";
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Accel-Buffering' => 'no',
            'Connection' => 'keep-alive',
        ]);
    }

    private function streamFromAPI(array $messages, string $model): \Generator
    {
        $response = Http::withToken(config('services.openrouter.api_key'))
            ->withHeaders([
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])
            ->withOptions(['stream' => true])
            ->timeout(120)
            ->post(rtrim(config('services.openrouter.base_url'), '/') . '/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'stream' => true,
            ]);

        if ($response->failed()) {
            yield ['type' => 'error', 'data' => $response->json('error.message', 'HTTP Error')];
            return;
        }

        $buffer = '';
        $body = $response->toPsrResponse()->getBody();

        while (!$body->eof()) {
            // Lire en petits morceaux pour un streaming plus réactif
            $chunk = $body->read(64);
            if ($chunk === '') {
                usleep(10000); // 10ms
                continue;
            }

            $buffer .= $chunk;

            while (($pos = strpos($buffer, "\n")) !== false) {
                $line = trim(substr($buffer, 0, $pos));
                $buffer = substr($buffer, $pos + 1);

                if ($line === '' || str_starts_with($line, ':')) {
                    continue;
                }

                if (str_starts_with($line, 'data: ')) {
                    $data = substr($line, 6);

                    if ($data === '[DONE]') {
                        yield ['type' => 'done', 'data' => null];
                        return;
                    }

                    try {
                        $parsed = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

                        if (isset($parsed['error'])) {
                            yield ['type' => 'error', 'data' => $parsed['error']['message'] ?? 'Unknown error'];
                            return;
                        }

                        $delta = $parsed['choices'][0]['delta'] ?? [];

                        if (!empty($delta['content'])) {
                            yield ['type' => 'content', 'data' => $delta['content']];
                        }
                    } catch (\JsonException $e) {
                        continue;
                    }
                }
            }
        }
    }

    private function getSystemPrompt(Conversation $conversation): array
    {
        $user = auth()->user();
        $userName = $user?->name ?? 'l\'utilisateur';
        $now = now()->locale('fr')->format('l d F Y H:i');

        // Custom instructions from user OR preset
        $customInstructions = '';

        if ($conversation->custom_instructions_about || $conversation->custom_instructions_behavior || $conversation->custom_instructions_commands) {
            $parts = [];
            if ($conversation->custom_instructions_about) $parts[] = "À propos de l'utilisateur :\n" . $conversation->custom_instructions_about;
            if ($conversation->custom_instructions_behavior) $parts[] = "Comportement attendu :\n" . $conversation->custom_instructions_behavior;
            if ($conversation->custom_instructions_commands) $parts[] = "Commandes personnalisées :\n" . $conversation->custom_instructions_commands;
            $customInstructions = !empty($parts) ? "\n\n" . implode("\n\n", $parts) : '';
        } elseif ($conversation->instruction_preset_id) {
            $preset = \App\Models\InstructionPreset::find($conversation->instruction_preset_id);
            if ($preset) {
                $parts = [];
                if ($preset->about) $parts[] = "À propos de l'utilisateur :\n" . $preset->about;
                if ($preset->behavior) $parts[] = "Comportement attendu :\n" . $preset->behavior;
                if ($preset->commands) $parts[] = "Commandes personnalisées :\n" . $preset->commands;
                $customInstructions = !empty($parts) ? "\n\n" . implode("\n\n", $parts) : '';
            }
        } elseif ($user) {
            $parts = [];
            if ($user->custom_instructions_about) $parts[] = "À propos de l'utilisateur :\n" . $user->custom_instructions_about;
            if ($user->custom_instructions_behavior) $parts[] = "Comportement attendu :\n" . $user->custom_instructions_behavior;
            if ($user->custom_instructions_commands) $parts[] = "Commandes personnalisées :\n" . $user->custom_instructions_commands;
            $customInstructions = !empty($parts) ? "\n\n" . implode("\n\n", $parts) : '';
        }

        $preferenceParts = [];
        if ($user) {
            $toneMap = [
                'default' => 'Par defaut',
                'cynical' => 'Cynique',
                'professional' => 'Professionnel',
                'friendly' => 'Amical',
                'coach' => 'Coach',
                'technical' => 'Technique',
            ];
            $concisenessMap = [
                'default' => 'Par defaut',
                'concise' => 'Concis',
                'balanced' => 'Equilibre',
                'detailed' => 'Detaille',
            ];
            $titlesListsMap = [
                'default' => 'Par defaut',
                'minimal' => 'Minimal',
                'standard' => 'Standard',
                'rich' => 'Riche',
            ];
            $levelMap = [
                'default' => 'Par defaut',
                'low' => 'Moins',
                'high' => 'Plus',
            ];
            $emojiMap = [
                'default' => 'Par defaut',
                'none' => 'Aucun',
                'low' => 'Peu',
                'high' => 'Plus',
            ];

            if ($user->tone_style) {
                $preferenceParts[] = 'Style et ton de base : ' . ($toneMap[$user->tone_style] ?? $user->tone_style);
            }
            if ($user->conciseness) {
                $preferenceParts[] = 'Concis : ' . ($concisenessMap[$user->conciseness] ?? $user->conciseness);
            }
            if ($user->titles_lists) {
                $preferenceParts[] = 'Titres et listes : ' . ($titlesListsMap[$user->titles_lists] ?? $user->titles_lists);
            }
            if ($user->warmth) {
                $preferenceParts[] = 'Chaleureux : ' . ($levelMap[$user->warmth] ?? $user->warmth);
            }
            if ($user->enthusiasm) {
                $preferenceParts[] = 'Enthousiaste : ' . ($levelMap[$user->enthusiasm] ?? $user->enthusiasm);
            }
            if ($user->formality) {
                $preferenceParts[] = 'Formel : ' . ($levelMap[$user->formality] ?? $user->formality);
            }
            if ($user->emojis) {
                $preferenceParts[] = 'Emojis : ' . ($emojiMap[$user->emojis] ?? $user->emojis);
            }
        }

        if (!empty($preferenceParts)) {
            $preferencesBlock = "Preferences de reponse :\n" . implode("\n", $preferenceParts);
            $customInstructions .= "\n\n" . $preferencesBlock;
        }

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
     * Générer automatiquement un titre pour la conversation.
     */
    private function generateTitle(Conversation $conversation, array $messages, string $lastResponse): void
    {
        try {
            // Trouver le premier message de l'utilisateur (ignorer les messages system)
            $firstUserMessage = null;
            foreach ($messages as $message) {
                if (isset($message['role']) && $message['role'] === 'user') {
                    $firstUserMessage = $message;
                    break;
                }
            }

            if (!$firstUserMessage || !isset($firstUserMessage['content'])) {
                $conversation->update(['title' => 'Nouvelle conversation']);
                return;
            }

            $firstMessageContent = is_array($firstUserMessage['content'])
                ? $firstUserMessage['content'][0]['text'] ?? 'Message avec image'
                : (is_string($firstUserMessage['content']) ? $firstUserMessage['content'] : 'Message');

            // Limiter le contenu du message pour éviter des prompts trop longs
            if (strlen($firstMessageContent) > 500) {
                $firstMessageContent = substr($firstMessageContent, 0, 500) . '...';
            }

            // Limiter la réponse aussi
            $responsePreview = strlen($lastResponse) > 500
                ? substr($lastResponse, 0, 500) . '...'
                : $lastResponse;

            $titlePrompt = [
                [
                    'role' => 'user',
                    'content' => "Génère un titre court (maximum 6 mots) pour cette conversation. Réponds uniquement avec le titre, sans guillemets, sans tirets, sans caractères spéciaux.\n\nPremier message : " . $firstMessageContent . "\n\nRéponse : " . $responsePreview
                ]
            ];

            $title = $this->chatService->sendMessage(
                messages: $titlePrompt,
                model: $conversation->model,
                temperature: 0.7
            );

            // Nettoyer le titre (enlever guillemets, tirets, etc.)
            $title = trim($title, " \n\r\t\v\0\"'-–—");

            // Limiter à 100 caractères max
            if (strlen($title) > 100) {
                $title = substr($title, 0, 97) . '...';
            }

            // S'assurer que le titre n'est pas vide
            if (empty($title)) {
                $title = 'Nouvelle conversation';
            }

            $conversation->update(['title' => $title]);
            \Log::info("Generated title for conversation {$conversation->id}: {$title}");
        } catch (\Exception $e) {
            // Si la génération du titre échoue, utiliser un titre par défaut
            \Log::error("Error generating title for conversation {$conversation->id}: " . $e->getMessage());
            $conversation->update(['title' => 'Nouvelle conversation']);
        }
    }
}
