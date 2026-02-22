<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

use App\Http\Controllers\AskController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserModelController;
use App\Http\Controllers\AskStreamController;
use App\Http\Controllers\CustomInstructionsController;
use App\Http\Controllers\PresetController;

Route::get('/ask', [AskController::class, 'index'])->middleware('auth')->name('ask.index');
Route::post('/ask', [AskController::class, 'ask'])->middleware('auth')->name('ask.post');

Route::get('/ask-stream', [AskStreamController::class, 'index'])->middleware('auth')->name('ask.stream.index');
Route::post('/ask-stream', [AskStreamController::class, 'stream'])->middleware('auth')->name('ask.stream.post');

// Routes pour le chat avec conversations
Route::middleware('auth')->group(function () {
    Route::resource('conversations', ConversationController::class);
    Route::patch('conversations/{conversation}/title', [ConversationController::class, 'updateTitle'])->name('conversations.updateTitle');
    Route::patch('conversations/{conversation}/custom-instructions', [ConversationController::class, 'updateCustomInstructions'])->name('conversations.updateCustomInstructions');
    Route::post('conversations/{conversation}/regenerate-title', [ConversationController::class, 'regenerateTitle'])->name('conversations.regenerateTitle');
    Route::post('conversations/{conversation}/messages', [MessageController::class, 'store'])->name('conversations.messages.store');
    Route::post('conversations/{conversation}/messages/stream', [MessageController::class, 'stream'])->name('conversations.messages.stream');
    Route::patch('users/model', [UserModelController::class, 'update'])->name('users.model.update');

    // Instructions personnalisées
    Route::get('custom-instructions', [CustomInstructionsController::class, 'edit'])->name('custom-instructions.edit');
    Route::patch('custom-instructions', [CustomInstructionsController::class, 'update'])->name('custom-instructions.update');

    // Presets d'instructions
    Route::get('presets', [PresetController::class, 'index'])->name('presets.index');
    Route::post('presets', [PresetController::class, 'store'])->name('presets.store');
    Route::delete('presets/{preset}', [PresetController::class, 'destroy'])->name('presets.destroy');

    // Diagnostic des modèles
    Route::get('models/status', function (App\Services\ChatService $chatService) {
        $models = $chatService->getModels();

        return response()->json([
            'total' => count($models),
            'free' => collect($models)->where('is_free', true)->count(),
            'with_images' => collect($models)->where('supports_image', true)->count(),
            'models' => collect($models)->map(fn($m) => [
                'id' => $m['id'],
                'name' => $m['name'],
                'is_free' => $m['is_free'],
                'supports_image' => $m['supports_image'],
                'pricing' => $m['pricing'],
            ])->all()
        ]);
    })->name('models.status');
});

Route::get('/', function () {
    $presets = \App\Models\InstructionPreset::system()->get();

    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
        'presets' => $presets,
    ]);
})->name('home');

Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

Route::get('/legal', function () {
    return Inertia::render('Legal');
})->name('legal');

Route::get('/privacy', function () {
    return Inertia::render('Privacy');
})->name('privacy');

Route::get('/ai-act', function () {
    return Inertia::render('AiAct');
})->name('ai-act');

// Sitemap dynamique
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

Route::get('dashboard', function () {
    $presets = \App\Models\InstructionPreset::system()->get();

    return Inertia::render('Dashboard', [
        'presets' => $presets,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
