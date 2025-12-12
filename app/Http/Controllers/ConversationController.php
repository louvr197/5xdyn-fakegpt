<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Services\SimpleAskService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class ConversationController extends Controller
{
    public function __construct(private SimpleAskService $askService) {}

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
            'selectedModel' => auth()->user()->last_model ?? SimpleAskService::DEFAULT_MODEL,
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

        $conversation->update([
            'model' => $request->model,
        ]);

        // Mettre à jour aussi le modèle par défaut de l'utilisateur
        auth()->user()->update([
            'last_model' => $request->model,
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
