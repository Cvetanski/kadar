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
            'name' => 'Creator Pro Plan',
            'tagline' => 'For freelancers who want steady, real client work',
            'monthly' => 14,
            'annual' => 140,
            'features' => [
                'Unlimited proposals — no daily cap',
                'More visibility to clients — priority placement in search',
                'Contact clients directly — skip waiting for an invite',
                'Job alerts for new matching projects',
                'AI cover letter writer for your proposals',
                'Pro badge on your profile',
                'Priority support — get help faster when you need it',
                'Cancel anytime',
            ],
        ],
        'client' => [
            'name' => 'Client Pro Plan',
            'tagline' => 'For clients who want to hire the right creator, fast',
            'monthly' => 14,
            'annual' => 140,
            'features' => [
                'Unlimited proposals on your projects — no 5-proposal cap',
                'Unlimited project invitations — no daily cap',
                'Priority placement for your projects in the creator feed',
                'Advanced search filters',
                'No hidden fees, no extra costs',
                'Cancel anytime',
            ],
        ],
    ];

    /**
     * Display copy for the free tier's limits, shown alongside the Pro
     * card on /pricing. Kept separate from PLANS since free has no price
     * and no checkout route.
     */
    public const FREE_PLANS = [
        'creator' => [
            'name' => 'Free',
            'tagline' => 'Get started and land your first clients',
            'features' => [
                '2 proposals per day',
                'Full portfolio & profile',
                'Unlimited browsing of open projects',
                'Direct messaging once a client invites you',
            ],
        ],
        'client' => [
            'name' => 'Free',
            'tagline' => 'Post a project and find the right creator',
            'features' => [
                '1 project (lifetime)',
                'Up to 5 proposals per project',
                'Browse the first 10 creators in search',
                'Message creators who apply to your project',
            ],
        ],
    ];

    public function pricing(Request $request): View
    {
        $lockedRole = Auth::user()?->role;
        $lockedRole = in_array($lockedRole, ['client', 'creator'], true) ? $lockedRole : null;

        $defaultRole = $lockedRole
            ?? (in_array($request->query('role'), ['client', 'creator'], true) ? $request->query('role') : 'client');

        return view('subscription.pricing', [
            'plans' => self::PLANS,
            'freePlans' => self::FREE_PLANS,
            'lockedRole' => $lockedRole,
            'defaultRole' => $defaultRole,
        ]);
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
