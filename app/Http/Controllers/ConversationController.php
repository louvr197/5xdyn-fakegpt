<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class ConversationController extends Controller
{
    public function __construct(private ChatService $askService) {}

    /**
     * Display a listing of conversations.
     */
    public function index()
    {
        $conversations = auth()->user()
            ->conversations()
            ->with(['latestMessage', 'instructionPreset'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $systemPresets = \App\Models\InstructionPreset::system()->get();
        $userPresets = \App\Models\InstructionPreset::userPresets(auth()->id())->get();

        return Inertia::render('Chat/Index', [
            'conversations' => $conversations,
            'models' => $this->askService->getModels(),
            'selectedModel' => auth()->user()->last_model ?? ChatService::DEFAULT_MODEL,
            'failedModels' => \App\Models\FailedModel::pluck('model_id')->toArray(),
            'systemPresets' => $systemPresets,
            'userPresets' => $userPresets,
        ]);
    }

    /**
     * Store a newly created conversation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'model' => 'required|string',
            'instruction_preset_id' => 'nullable|exists:instruction_presets,id',
        ]);

        $conversation = auth()->user()->conversations()->create([
            'model' => $request->model,
            'title' => null, // Sera généré après le premier message
            'instruction_preset_id' => $request->instruction_preset_id,
        ]);

        // Mettre à jour le modèle par défaut de l'utilisateur
        auth()->user()->update(['last_model' => $request->model]);

        return redirect()->route('conversations.show', $conversation);
    }

    /**
     * Display the specified conversation.
     */
    public function show(Conversation $conversation)
    {
        // Vérifier que la conversation appartient à l'utilisateur
        Gate::authorize('view', $conversation);

        $conversation = $conversation->load(['messages' => function($q) {
            $q->orderBy('created_at', 'asc');
        }, 'instructionPreset']);

        $conversations = auth()->user()
            ->conversations()
            ->with(['latestMessage', 'instructionPreset'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $systemPresets = \App\Models\InstructionPreset::system()->get();
        $userPresets = \App\Models\InstructionPreset::userPresets(auth()->id())->get();

        return Inertia::render('Chat/Index', [
            'conversations' => $conversations,
            'currentConversation' => [
                ...$conversation->toArray(),
                'messages' => $conversation->messages->toArray(),
            ],
            'models' => $this->askService->getModels(),
            'selectedModel' => $conversation->model,
            'failedModels' => \App\Models\FailedModel::pluck('model_id')->toArray(),
            'systemPresets' => $systemPresets,
            'userPresets' => $userPresets,
        ]);
    }

    /**
     * Update the conversation model.
     */
    public function update(Request $request, Conversation $conversation)
    {
        Gate::authorize('update', $conversation);

        $request->validate([
            'model' => 'required|string',
        ]);

        $oldModel = $conversation->model;
        $newModel = $request->model;

        // Si le modèle change, ajouter un message système
        if ($oldModel !== $newModel) {
            // Obtenir les noms des modèles
            $models = $this->askService->getModels();
            $oldModelName = collect($models)->firstWhere('id', $oldModel)['name'] ?? $oldModel;
            $newModelName = collect($models)->firstWhere('id', $newModel)['name'] ?? $newModel;

            $conversation->messages()->create([
                'role' => 'system',
                'content' => "📝 Modèle changé de **{$oldModelName}** vers **{$newModelName}**",
            ]);
        }

        $conversation->update([
            'model' => $newModel,
        ]);

        // Mettre à jour aussi le modèle par défaut de l'utilisateur
        auth()->user()->update([
            'last_model' => $newModel,
        ]);

        return back();
    }

    /**
     * Regenerate the conversation title using AI.
     */
    public function regenerateTitle(Request $request, Conversation $conversation)
    {
        Gate::authorize('update', $conversation);

        try {
            // Récupérer les messages de la conversation
            $messages = $conversation->messages()
                ->orderBy('created_at', 'asc')
                ->take(10) // Prendre les 10 premiers messages pour avoir le contexte
                ->get();

            if ($messages->isEmpty()) {
                return back()->with('error', 'Aucun message dans la conversation');
            }

            // Construire un résumé de la conversation pour l'IA
            $conversationSummary = $messages->map(function ($msg) {
                $content = json_decode($msg->content, true) ?? $msg->content;
                if (is_array($content)) {
                    $content = $content[0]['text'] ?? 'Message avec image';
                }
                $role = $msg->role === 'user' ? 'Utilisateur' : 'Assistant';
                return "{$role}: " . substr($content, 0, 200);
            })->join("\n");

            // Demander à l'IA de générer un titre
            $titlePrompt = [
                [
                    'role' => 'user',
                    'content' => "Génère un titre court et descriptif (maximum 6 mots) pour cette conversation. Réponds uniquement avec le titre, sans guillemets ni ponctuation finale.\n\nConversation :\n" . $conversationSummary
                ]
            ];

            $title = $this->askService->sendMessage(
                messages: $titlePrompt,
                model: $conversation->model,
                temperature: 0.7
            );

            // Nettoyer le titre
            $title = trim($title, " \n\r\t\v\0\"'");

            // Limiter à 100 caractères max
            if (strlen($title) > 100) {
                $title = substr($title, 0, 97) . '...';
            }

            $conversation->update(['title' => $title]);

            return back()->with('success', 'Titre régénéré avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération du titre: ' . $e->getMessage());
        }
    }

    /**
     * Update the conversation title.
     */
    public function updateTitle(Request $request, Conversation $conversation)
    {
        Gate::authorize('update', $conversation);

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $conversation->update([
            'title' => $request->title,
        ]);

        return back();
    }

    /**
     * Update the conversation custom instructions.
     */
    public function updateCustomInstructions(Request $request, Conversation $conversation)
    {
        Gate::authorize('update', $conversation);

        $validated = $request->validate([
            'custom_instructions_about' => 'nullable|string|max:5000',
            'custom_instructions_behavior' => 'nullable|string|max:5000',
            'custom_instructions_commands' => 'nullable|string|max:5000',
        ]);

        $conversation->update($validated);

        return back()->with('success', 'Instructions du chat mises à jour.');
    }

    /**
     * Remove the specified conversation.
     */
    public function destroy(Conversation $conversation)
    {
        // Vérifier que la conversation appartient à l'utilisateur
        Gate::authorize('delete', $conversation);

        $conversation->delete();

        return redirect()->route('conversations.index');
    }
}
