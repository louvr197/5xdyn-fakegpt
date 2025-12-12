<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

use App\Http\Controllers\AskController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserModelController;

Route::get('/ask', [AskController::class, 'index'])->middleware('auth')->name('ask.index');
Route::post('/ask', [AskController::class, 'ask'])->middleware('auth')->name('ask.post');

// Routes pour le chat avec conversations
Route::middleware('auth')->group(function () {
    Route::resource('conversations', ConversationController::class);
    Route::post('conversations/{conversation}/messages', [MessageController::class, 'store'])->name('conversations.messages.store');
    Route::patch('users/model', [UserModelController::class, 'update'])->name('users.model.update');
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
