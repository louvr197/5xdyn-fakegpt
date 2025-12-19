<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SimpleAskStreamService;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;


class AskStreamController extends Controller
{
    //
    public function __construct(private SimpleAskStreamService $streamService)
    {

    }

    public function index(): Response{
        return Inertia::render('AskStream/Index',[
            'models' => $this->streamService->getModels(),
            'selectedModel' => auth()->user()->last_model ?? SimpleAskStreamService::DEFAULT_MODEL,
        ]);
    }

    public function stream(Request $request): StreamedResponse{
        $validated = $request->validate([
            'message'=> 'required|string|max:100000',
            'model' => 'required|string',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'reasoning_effort' => 'nullable|string|in:none,low,medium,high',

        ]);
        $messages = [
            [
                'role' => 'user',
                'content' => $validated['message'],
            ]
        ];
        $model = $validated['model'];
        $temperature = $validated['temperature'] ?? 1.0;
        $reasoningEffort = $validated['reasoning_effort'] ?? null;

        return response()->stream(function() use ($messages, $model, $temperature, $reasoningEffort){
            $this->streamService->streamToOutput($messages, $model, $temperature, $reasoningEffort);},headers: [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            ]);

    }
}
