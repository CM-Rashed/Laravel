<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    public function index()
    {
        return view('home');
    }   

    public function generate(Request $request)
    {
        // Validate the input
        $request->validate([
            'prompt' => 'required|string|max:255',
        ]);

        $prompt = $request->input('prompt');

        // Here you would typically call your AI service to generate a response based on the prompt.
        // For demonstration purposes, we'll just return a dummy response.
      $apiKey = config('services.gemini.key', env('GEMINI_API_KEY'));
$model = 'gemini-1.5-flash'; // or 'gemini-1.5-pro'

$response = Http::withHeaders([
    'Content-Type' => 'application/json',
])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
    'contents' => [
        [
            'parts' => [
                ['text' => $prompt]
            ]
        ]
    ]
]);

        return view('home', ['response' => $response]);
    }
}
