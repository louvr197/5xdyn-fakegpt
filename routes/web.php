<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

use App\Http\Controllers\AskController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserModelController;
use App\Http\Controllers\AskStreamController;

Route::get('/ask', [AskController::class, 'index'])->middleware('auth')->name('ask.index');
Route::post('/ask', [AskController::class, 'ask'])->middleware('auth')->name('ask.post');

Route::get('/ask-stream', [AskStreamController::class, 'index'])->middleware('auth')->name('ask.stream.index');
Route::post('/ask-stream', [AskStreamController::class, 'stream'])->middleware('auth')->name('ask.stream.post');

// Routes pour le chat avec conversations
Route::middleware('auth')->group(function () {
    Route::resource('conversations', ConversationController::class);
    Route::post('conversations/{conversation}/messages', [MessageController::class, 'store'])->name('conversations.messages.store');
    Route::patch('users/model', [UserModelController::class, 'update'])->name('users.model.update');

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
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');


Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
