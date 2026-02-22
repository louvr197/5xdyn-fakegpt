<?php

namespace App\Http\Controllers;

use App\Models\InstructionPreset;
use Illuminate\Http\Request;

class PresetController extends Controller
{
    public function index()
    {
        $systemPresets = InstructionPreset::system()->get();
        $userPresets = InstructionPreset::userPresets(auth()->id())->get();

        return response()->json([
            'system' => $systemPresets,
            'user' => $userPresets,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'required|string|max:50',
            'about' => 'nullable|string|max:5000',
            'behavior' => 'nullable|string|max:5000',
            'commands' => 'nullable|string|max:5000',
            'preferred_model' => 'nullable|string|max:255',
        ]);

        $preset = auth()->user()->instructionPresets()->create([
            ...$validated,
            'is_system' => false,
        ]);

        return response()->json($preset, 201);
    }

    public function destroy(InstructionPreset $preset)
    {
        // Vérifier que l'utilisateur possède ce preset et que ce n'est pas un preset système
        if ($preset->user_id !== auth()->id() || $preset->is_system) {
            abort(403, 'Vous ne pouvez pas supprimer ce preset.');
        }

        $preset->delete();

        return response()->json(null, 204);
    }
}
