<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint'        => 'required|string',
            'keys.public_key' => 'required|string',
            'keys.auth_token' => 'required|string',
        ]);

        PushSubscription::updateOrCreate(
            ['endpoint' => $validated['endpoint']],
            [
                'user_id'          => $request->user()->id,
                'public_key'       => $validated['keys']['public_key'],
                'auth_token'       => $validated['keys']['auth_token'],
                'content_encoding' => 'aes128gcm',
            ]
        );

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $query = PushSubscription::where('user_id', $request->user()->id);

        if ($endpoint = $request->input('endpoint')) {
            $query->where('endpoint', $endpoint);
        }

        $query->delete();

        return response()->json(['success' => true]);
    }

    public function publicKey(): JsonResponse
    {
        return response()->json([
            'publicKey' => config('webpush.vapid.public_key'),
        ]);
    }
}
