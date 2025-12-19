<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Services\ChatService;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
     * Générer automatiquement un titre pour la conversation.
     */
    private function generateTitle(Conversation $conversation, array $messages, string $lastResponse): void
    {
        try {
            $firstMessageContent = is_array($messages[0]['content'])
                ? $messages[0]['content'][0]['text'] ?? 'Message avec image'
                : $messages[0]['content'];

            $titlePrompt = [
                [
                    'role' => 'user',
                    'content' => "Génère un titre court (maximum 6 mots) pour cette conversation. Réponds uniquement avec le titre, sans guillemets ni ponctuation finale.\n\nPremier message : " . $firstMessageContent . "\n\nRéponse : " . $lastResponse
                ]
            ];

            $title = $this->chatService->sendMessage(
                messages: $titlePrompt,
                model: $conversation->model,
                temperature: 0.7
            );

            // Nettoyer le titre (enlever guillemets, etc.)
            $title = trim($title, " \n\r\t\v\0\"'");

            // Limiter à 100 caractères max
            if (strlen($title) > 100) {
                $title = substr($title, 0, 97) . '...';
            }

            $conversation->update(['title' => $title]);
        } catch (\Exception $e) {
            // Si la génération du titre échoue, utiliser un titre par défaut
            $conversation->update(['title' => 'Nouvelle conversation']);
        }
    }
}
