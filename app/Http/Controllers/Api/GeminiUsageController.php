<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GeminiUsageLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeminiUsageController extends Controller
{
    /**
     * Receive Gemini usage data from the n8n workflow after each AI call.
     * Authenticated via a shared bearer token (GEMINI_WEBHOOK_TOKEN env).
     */
    public function store(Request $request): JsonResponse
    {
        $token = config('services.gemini.webhook_token');
        if ($token && $request->bearerToken() !== $token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'phone' => ['nullable', 'string', 'max:50'],
            'push_name' => ['nullable', 'string', 'max:255'],
            'customer_message' => ['nullable', 'string'],
            'ai_reply' => ['nullable', 'string'],
            'prompt_tokens' => ['nullable', 'integer', 'min:0'],
            'output_tokens' => ['nullable', 'integer', 'min:0'],
            'thoughts_tokens' => ['nullable', 'integer', 'min:0'],
            'total_tokens' => ['nullable', 'integer', 'min:0'],
            'service_tier' => ['nullable', 'string', 'max:50'],
            'model' => ['nullable', 'string', 'max:100'],
            'latency_ms' => ['nullable', 'integer', 'min:0'],
            'success' => ['nullable', 'boolean'],
            'error' => ['nullable', 'string'],
        ]);

        $log = GeminiUsageLog::create([
            'phone' => $validated['phone'] ?? null,
            'push_name' => $validated['push_name'] ?? null,
            'customer_message' => $validated['customer_message'] ?? null,
            'ai_reply' => $validated['ai_reply'] ?? null,
            'prompt_tokens' => $validated['prompt_tokens'] ?? 0,
            'output_tokens' => $validated['output_tokens'] ?? 0,
            'thoughts_tokens' => $validated['thoughts_tokens'] ?? 0,
            'total_tokens' => $validated['total_tokens'] ?? 0,
            'service_tier' => $validated['service_tier'] ?? null,
            'model' => $validated['model'] ?? null,
            'latency_ms' => $validated['latency_ms'] ?? null,
            'success' => $validated['success'] ?? true,
            'error' => $validated['error'] ?? null,
        ]);

        return response()->json(['status' => 'ok', 'id' => $log->id], 201);
    }
}
