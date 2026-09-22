<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * The plans and prices shown on /pricing. Display copy only — the
     * actual Paddle price IDs are resolved separately via priceId(),
     * since live and sandbox use different catalogs.
     */
    public const PLANS = [
        'creator' => [
            'name' => 'Creator Plan',
            'tagline' => 'For freelancers who want steady, real client work',
            'monthly' => 14,
            'annual' => 140,
            'features' => [
                'Full access to browse every open project',
                'Unlimited proposals — no monthly cap',
                'Direct messaging with clients',
                'Verified profile badge that builds trust',
                'Priority placement in client search results',
                'Portfolio showcase seen by every client',
                'No hidden fees, no extra costs',
                'Cancel anytime — no long-term contract',
            ],
        ],
        'client' => [
            'name' => 'Client Plan',
            'tagline' => 'For clients who want to hire the right creator, fast',
            'monthly' => 14,
            'annual' => 140,
            'features' => [
                'Unlimited creator search & filtering',
                'Post unlimited projects',
                'Direct messaging with creators',
                'Invite creators straight to your projects',
                'See verified reviews before you hire',
                'No limit on active projects at once',
                'No hidden fees, no extra costs',
                'Cancel anytime — no long-term contract',
            ],
        ],
    ];

    public function pricing(): View
    {
        $plans = self::PLANS;

        $role = Auth::user()?->role;

        if (in_array($role, ['client', 'creator'], true)) {
            $plans = array_intersect_key($plans, [$role => true]);
        }

        return view('subscription.pricing', [
            'plans' => $plans,
        ]);
    }

    public function upgrade(): View
    {
        return view('subscription.upgrade');
    }

    public function checkout(string $plan): View
    {
        [$planKey, $billingPeriod] = array_pad(explode('-', $plan, 2), 2, null);

        abort_unless(
            array_key_exists($planKey, self::PLANS) && in_array($billingPeriod, ['monthly', 'annual'], true),
            404
        );

        return view('subscription.checkout', [
            'plan' => self::PLANS[$planKey],
            'planKey' => $planKey,
            'billingPeriod' => $billingPeriod,
            'priceId' => self::priceId($planKey, $billingPeriod),
            ...$this->paddleJsConfig(),
        ]);
    }

    public static function priceId(string $planKey, string $billingPeriod): ?string
    {
        $map = config('services.paddle.sandbox') ? 'sandbox_prices' : 'prices';

        return config("services.paddle.{$map}.{$planKey}_{$billingPeriod}");
    }

    /**
     * Reverses a Paddle price ID back into {plan, billingPeriod} for
     * display on the billing page — e.g. "Creator Plan · Monthly".
     */
    public static function planFromPriceId(?string $priceId): ?array
    {
        if (! $priceId) {
            return null;
        }

        $map = config('services.paddle.sandbox') ? 'sandbox_prices' : 'prices';

        foreach (config("services.paddle.{$map}", []) as $key => $id) {
            if ($id === $priceId) {
                [$planKey, $billingPeriod] = explode('_', $key, 2);

                return [
                    'plan' => self::PLANS[$planKey] ?? null,
                    'billingPeriod' => $billingPeriod,
                ];
            }
        }

        return null;
    }

    public function billing(Request $request): View
    {
        $user = $request->user();

        return view('subscription.billing', [
            'subscription' => self::planFromPriceId($user->paddle_price_id),
            'status' => $user->paddle_status,
            'subscribedUntil' => $user->subscribed_until,
            'isLegacyFree' => $user->is_legacy_free,
        ]);
    }

    public function cancel(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->paddle_subscription_id, 404);

        $base = config('services.paddle.sandbox') ? 'https://sandbox-api.paddle.com' : 'https://api.paddle.com';

        $response = Http::withToken(config('services.paddle.api_key'))
            ->post("{$base}/subscriptions/{$user->paddle_subscription_id}/cancel", [
                'effective_from' => 'next_billing_period',
            ]);

        if (! $response->successful()) {
            Log::error('Paddle subscription cancel failed', [
                'user_id' => $user->id,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return redirect()->route('billing')->with('error', "Couldn't cancel your subscription — please try again or contact support.");
        }

        // Optimistic: Paddle applies this immediately even though the
        // webhook confirming it may take a moment to arrive. Access itself
        // is untouched — it naturally lapses on subscribed_until.
        $user->update(['paddle_status' => 'canceled']);

        return redirect()->route('billing')->with('status', 'Your subscription is set to cancel at the end of the current billing period.');
    }

    private function paddleJsConfig(): array
    {
        return [
            'paddleClientToken' => config('services.paddle.client_side_token'),
            'paddleSandbox' => (bool) config('services.paddle.sandbox'),
        ];
    }
}
