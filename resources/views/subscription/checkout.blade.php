<x-app-layout :title="'Checkout'" description="Checkout for CreatorSpot subscriptions.">
    <div class="py-16">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <style>
                .co-card{background:#fff;border:1px solid #E8EBF0;border-radius:20px;padding:40px 32px;text-align:center;}
                .co-icon{
                    width:56px;height:56px;border-radius:16px;margin:0 auto 20px;display:flex;align-items:center;
                    justify-content:center;font-size:26px;background:#F6F8FB;border:1px solid #E8EBF0;
                }
                .co-icon.is-spinning{animation:co-spin 1s linear infinite;}
                @keyframes co-spin{to{transform:rotate(360deg);}}
                .co-card h1{font-size:22px;font-weight:800;color:#14171F;letter-spacing:-0.01em;margin-bottom:10px;}
                .co-card p{font-size:14.5px;color:#666B76;line-height:1.6;margin-bottom:8px;}
                .co-plan{
                    display:inline-block;background:#EAF2FE;color:#0958B5;font-size:12.5px;font-weight:700;
                    padding:6px 14px;border-radius:999px;margin:12px 0 28px;
                }
                .co-btn{
                    display:inline-block;background:#fff;color:#14171F;border:1px solid #E8EBF0;
                    border-radius:12px;padding:12px 24px;font-weight:700;font-size:13.5px;font-family:'Inter',sans-serif;
                    text-decoration:none;margin:4px;cursor:pointer;
                }
                .co-btn-primary{
                    background:linear-gradient(135deg,#2D82E8,#0958B5);color:#fff;border:none;
                    box-shadow:0 6px 18px rgba(11,111,224,.28);
                }
            </style>

            @if (! $paddleClientToken || ! $priceId)
                <div class="co-card">
                    <div class="co-icon">⚠️</div>
                    <h1>Checkout isn't configured yet</h1>
                    <p>Billing isn't fully set up on this environment yet. Try again shortly.</p>
                    <div class="co-plan">{{ $plan['name'] }} · {{ $billingPeriod === 'annual' ? '$'.$plan['annual'].'/yr' : '$'.$plan['monthly'].'/mo' }}</div>
                    <div><a href="{{ route('pricing') }}" class="co-btn">← Back to plans</a></div>
                </div>
            @else
                <div class="co-card" x-data="paddleCheckout()" x-init="init()">
                    <template x-if="state === 'loading'">
                        <div>
                            <div class="co-icon is-spinning">⏳</div>
                            <h1>Opening secure checkout…</h1>
                            <p>Hang tight, this only takes a second.</p>
                        </div>
                    </template>

                    <template x-if="state === 'ready'">
                        <div>
                            <div class="co-icon">💳</div>
                            <h1>Ready when you are</h1>
                            <p>Your checkout window should have opened. Didn't see it?</p>
                            <div class="co-plan">{{ $plan['name'] }} · {{ $billingPeriod === 'annual' ? '$'.$plan['annual'].'/yr' : '$'.$plan['monthly'].'/mo' }}</div>
                            <div>
                                <button type="button" class="co-btn co-btn-primary" @click="open()">Open checkout</button>
                                <a href="{{ route('pricing') }}" class="co-btn">← Back to plans</a>
                            </div>
                        </div>
                    </template>

                    <template x-if="state === 'completed'">
                        <div>
                            <div class="co-icon">✅</div>
                            <h1>You're all set!</h1>
                            <p>Activating your subscription — taking you to your dashboard…</p>
                        </div>
                    </template>

                    <template x-if="state === 'error'">
                        <div>
                            <div class="co-icon">⚠️</div>
                            <h1>Checkout couldn't load</h1>
                            <p>Something blocked the checkout script. Please refresh or try again.</p>
                            <div><a href="{{ route('pricing') }}" class="co-btn">← Back to plans</a></div>
                        </div>
                    </template>
                </div>

                <script src="https://cdn.paddle.com/paddle/v2/paddle.js"></script>
                <script>
                    function paddleCheckout() {
                        return {
                            state: 'loading',
                            init() {
                                if (typeof Paddle === 'undefined') {
                                    this.state = 'error';
                                    return;
                                }

                                @if ($paddleSandbox)
                                    Paddle.Environment.set('sandbox');
                                @endif

                                Paddle.Initialize({
                                    token: @json($paddleClientToken),
                                    eventCallback: (event) => {
                                        if (event.name === 'checkout.completed') {
                                            this.state = 'completed';
                                            setTimeout(() => { window.location = @json(route('dashboard')); }, 2000);
                                        } else if (event.name === 'checkout.closed') {
                                            this.state = 'ready';
                                        }
                                    },
                                });

                                this.state = 'ready';
                                this.open();
                            },
                            open() {
                                Paddle.Checkout.open({
                                    items: [{ priceId: @json($priceId), quantity: 1 }],
                                    customer: { email: @json(Auth::user()->email) },
                                    customData: { user_id: @json(Auth::id()) },
                                    settings: { successUrl: @json(route('dashboard')) },
                                });
                            },
                        };
                    }
                </script>
            @endif
        </div>
    </div>
</x-app-layout>
