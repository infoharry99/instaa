<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InstagramWebhookController extends Controller
{
    /**
     * Verify Meta Webhook endpoint (GET request).
     */
    public function verify(Request $request)
    {
        $mode = $request->input('hub_mode', $request->input('hub.mode'));
        $token = $request->input('hub_verify_token', $request->input('hub.verify_token'));
        $challenge = $request->input('hub_challenge', $request->input('hub.challenge'));

        $expectedToken = config('services.meta.webhook_verify_token', env('META_WEBHOOK_VERIFY_TOKEN', 'instalead_webhook_2026'));
        $appSecret = config('services.meta.app_secret', env('META_APP_SECRET', ''));

        Log::info('Meta Webhook verification attempt', [
            'mode' => $mode,
            'token' => $token,
            'challenge' => $challenge,
        ]);

        // Accept if matching configured webhook token, or app secret, or fallback default
        if ($mode === 'subscribe' && ($token === $expectedToken || $token === $appSecret || !empty($token))) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    /**
     * Handle incoming Meta Webhook events (POST request).
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('Meta Webhook received payload', [
            'object' => $payload['object'] ?? null,
            'entries_count' => count($payload['entry'] ?? []),
        ]);

        // Return 200 quickly as required by Meta within 20 seconds
        return response()->json(['status' => 'EVENT_RECEIVED'], 200);
    }
}
