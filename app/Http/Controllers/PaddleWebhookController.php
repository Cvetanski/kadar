<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PaddleWebhookController extends Controller
{
    /**
     * Handles Paddle Billing webhook events. Only the subscription lifecycle
     * is processed for now — that's all this app currently needs.
     *
     * @see https://developer.paddle.com/webhooks/signature-verification
     */
    public function handle(Request $request): Response
    {
        $rawBody = $request->getContent();

        if (! $this->hasValidSignature($request, $rawBody)) {
            Log::warning('Paddle webhook rejected: invalid or missing signature.');

            return response('Invalid signature', 401);
        }

        $payload = json_decode($rawBody, true);
        $eventType = $payload['event_type'] ?? '';
        $data = $payload['data'] ?? [];

        Log::info('Paddle webhook received', ['event_type' => $eventType]);

        if (str_starts_with($eventType, 'subscription.')) {
            $this->syncSubscription($data);
        }

        return response('OK', 200);
    }

    private function hasValidSignature(Request $request, string $rawBody): bool
    {
        $secret = config('services.paddle.webhook_secret');
        $header = $request->header('Paddle-Signature');

        if (! $secret || ! $header) {
            return false;
        }

        // Header looks like "ts=1671552777;h1=<hmac>".
        $parts = [];
        foreach (explode(';', $header) as $segment) {
            [$key, $value] = array_pad(explode('=', $segment, 2), 2, null);
            $parts[$key] = $value;
        }

        if (empty($parts['ts']) || empty($parts['h1'])) {
            return false;
        }

        $expected = hash_hmac('sha256', "{$parts['ts']}:{$rawBody}", $secret);

        return hash_equals($expected, $parts['h1']);
    }

    /**
     * Runs for every subscription.* event (created, activated, updated,
     * canceled, paused, ...). Status and IDs are always recorded so the
     * billing page reflects reality, but subscribed_until — the actual
     * access gate — is only ever extended while Paddle reports the
     * subscription as active/trialing.
     *
     * This is deliberately the ONLY place access is granted: a failed
     * renewal, a cancellation, or a pause all just mean "stop extending"
     * here, and access quietly lapses on the date already paid for. Paddle
     * itself owns retrying failed payments (dunning) — we don't need our
     * own logic for that, only to react correctly to the status it reports.
     */
    private function syncSubscription(array $data): void
    {
        $user = $this->resolveUser($data);

        if (! $user) {
            Log::warning('Paddle webhook: could not resolve user for subscription.', [
                'custom_data' => $data['custom_data'] ?? null,
                'subscription_id' => $data['id'] ?? null,
            ]);

            return;
        }

        $status = $data['status'] ?? null;

        $update = [
            'paddle_customer_id' => $data['customer_id'] ?? $user->paddle_customer_id,
            'paddle_subscription_id' => $data['id'] ?? $user->paddle_subscription_id,
            'paddle_status' => $status,
        ];

        if (in_array($status, ['active', 'trialing'], true)) {
            $update['subscribed_until'] = $data['current_billing_period']['ends_at'] ?? $user->subscribed_until;
            $update['paddle_price_id'] = $data['items'][0]['price']['id'] ?? $user->paddle_price_id;
        }

        $user->update($update);
    }

    private function resolveUser(array $data): ?User
    {
        $userId = $data['custom_data']['user_id'] ?? null;

        if ($userId && $user = User::find($userId)) {
            return $user;
        }

        // Fallback for events where custom_data doesn't come through (e.g.
        // if it was ever set differently) — match by subscription id instead.
        if ($subscriptionId = $data['id'] ?? null) {
            return User::where('paddle_subscription_id', $subscriptionId)->first();
        }

        return null;
    }
}
