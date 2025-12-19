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
            ->with('latestMessage')
            ->orderBy('updated_at', 'desc')
            ->get();

        return Inertia::render('Chat/Index', [
            'conversations' => $conversations,
            'models' => $this->askService->getModels(),
            'selectedModel' => auth()->user()->last_model ?? ChatService::DEFAULT_MODEL,
            'failedModels' => \App\Models\FailedModel::pluck('model_id')->toArray(),
        ]);
    }

    /**
     * Store a newly created conversation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'model' => 'required|string',
        ]);

        $conversation = auth()->user()->conversations()->create([
            'model' => $request->model,
            'title' => null, // Sera généré après le premier message
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

        $conversation->load('messages');

        $conversations = auth()->user()
            ->conversations()
            ->with('latestMessage')
            ->orderBy('updated_at', 'desc')
            ->get();

        return Inertia::render('Chat/Index', [
            'conversations' => $conversations,
            'currentConversation' => $conversation,
            'models' => $this->askService->getModels(),
            'selectedModel' => $conversation->model,
            'failedModels' => \App\Models\FailedModel::pluck('model_id')->toArray(),
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
