<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Services\SimpleAskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MessageController extends Controller
{
    public function __construct(private SimpleAskService $askService) {}

    /**
     * Store a new message and get AI response.
     */
    public function store(Request $request, Conversation $conversation)
    {
        // Vérifier que la conversation appartient à l'utilisateur
        Gate::authorize('view', $conversation);

        $request->validate([
            'content' => 'required|string',
        ]);

        // Sauvegarder le message de l'utilisateur
        $userMessage = $conversation->messages()->create([
            'role' => 'user',
            'content' => $request->content,
        ]);

        try {
            // Récupérer tous les messages de la conversation pour le contexte
            $messages = $conversation->messages()
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(fn($msg) => [
                    'role' => $msg->role,
                    'content' => $msg->content,
                ])
                ->toArray();

            // Appeler l'API
            $response = $this->askService->sendMessage(
                messages: $messages,
                model: $conversation->model
            );

            // Sauvegarder la réponse de l'assistant
            $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $response,
            ]);

            // Générer le titre si c'est le premier message
            if ($conversation->messages()->count() === 2 && !$conversation->title) {
                $this->generateTitle($conversation, $messages, $response);
            }

            // Mettre à jour le timestamp de la conversation
            $conversation->touch();

        } catch (\Exception $e) {
            // En cas d'erreur, retourner avec le message d'erreur
            return back()->with('error', $e->getMessage());
        }

        return back();
    }

    /**
     * Générer automatiquement un titre pour la conversation.
     */
    private function generateTitle(Conversation $conversation, array $messages, string $lastResponse): void
    {
        try {
            $titlePrompt = [
                [
                    'role' => 'user',
                    'content' => "Génère un titre court (maximum 6 mots) pour cette conversation. Réponds uniquement avec le titre, sans guillemets ni ponctuation finale.\n\nPremier message : " . $messages[0]['content'] . "\n\nRéponse : " . $lastResponse
                ]
            ];

            $title = $this->askService->sendMessage(
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
