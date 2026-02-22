<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomInstructionsController extends Controller
{
    /**
     * Affiche le formulaire d'édition des instructions personnalisées.
     */
    public function edit(): Response
    {
        return Inertia::render('CustomInstructions/Edit', [
            'customInstructions' => [
                'about' => auth()->user()->custom_instructions_about,
                'behavior' => auth()->user()->custom_instructions_behavior,
                'commands' => auth()->user()->custom_instructions_commands,
            ],
            'personalization' => [
                'tone_style' => auth()->user()->tone_style,
                'conciseness' => auth()->user()->conciseness,
                'titles_lists' => auth()->user()->titles_lists,
                'warmth' => auth()->user()->warmth,
                'enthusiasm' => auth()->user()->enthusiasm,
                'formality' => auth()->user()->formality,
                'emojis' => auth()->user()->emojis,
            ],
        ]);
    }

    /**
     * Met à jour les instructions personnalisées.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'custom_instructions_about' => 'nullable|string|max:5000',
            'custom_instructions_behavior' => 'nullable|string|max:5000',
            'custom_instructions_commands' => 'nullable|string|max:5000',
            'tone_style' => 'nullable|string|max:50',
            'conciseness' => 'nullable|string|max:50',
            'titles_lists' => 'nullable|string|max:50',
            'warmth' => 'nullable|string|max:50',
            'enthusiasm' => 'nullable|string|max:50',
            'formality' => 'nullable|string|max:50',
            'emojis' => 'nullable|string|max:50',
        ]);

        auth()->user()->update($validated);

        return back()->with('success', 'Instructions personnalisées mises à jour avec succès.');
    }
}
