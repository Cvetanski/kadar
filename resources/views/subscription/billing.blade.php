<x-app-layout :title="'Billing'" description="Manage your CreatorSpot subscription.">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Billing</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <style>
                .bl-card{background:#fff;border:1px solid #E8EBF0;border-radius:20px;padding:32px;}
                .bl-row{display:flex;justify-content:space-between;align-items:center;padding:14px 0;border-bottom:1px solid #F1F3F6;}
                .bl-row:last-of-type{border-bottom:none;}
                .bl-label{font-size:13px;color:#9AA0AB;font-weight:600;}
                .bl-value{font-size:14.5px;color:#14171F;font-weight:700;}
                .bl-badge{
                    display:inline-block;font-size:11.5px;font-weight:800;padding:4px 10px;border-radius:999px;
                    text-transform:uppercase;letter-spacing:.02em;
                }
                .bl-badge-active{background:#E7F8F1;color:#17A673;}
                .bl-badge-canceled{background:#FDEEEC;color:#E5484D;}
                .bl-badge-past_due{background:#FEF3E2;color:#B45309;}
                .bl-badge-none{background:#F1F3F6;color:#666B76;}
                .bl-btn{
                    display:inline-block;background:#fff;color:#E5484D;border:1px solid #F3C9C8;
                    border-radius:12px;padding:12px 20px;font-weight:700;font-size:13.5px;font-family:'Inter',sans-serif;
                    cursor:pointer;margin-top:24px;
                }
                .bl-btn-primary{
                    display:inline-block;background:linear-gradient(135deg,#2D82E8,#0958B5);color:#fff;border:none;
                    border-radius:12px;padding:12px 24px;font-weight:700;font-size:13.5px;font-family:'Inter',sans-serif;
                    text-decoration:none;margin-top:24px;
                }
                .bl-note{font-size:12.5px;color:#9AA0AB;margin-top:12px;line-height:1.6;}
            </style>

            @if (session('status'))
                <div style="background:#E7F8F1;color:#17A673;font-size:13.5px;font-weight:600;padding:12px 16px;border-radius:10px;margin-bottom:16px;">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div style="background:#FDEEEC;color:#E5484D;font-size:13.5px;font-weight:600;padding:12px 16px;border-radius:10px;margin-bottom:16px;">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bl-card">
                @if ($isLegacyFree)
                    <div class="bl-row">
                        <span class="bl-label">Plan</span>
                        <span class="bl-value">Free (legacy account)</span>
                    </div>
                    <p class="bl-note">Your account was created before subscriptions launched, so you keep full access for free — no billing involved.</p>
                @elseif ($subscription)
                    <div class="bl-row">
                        <span class="bl-label">Plan</span>
                        <span class="bl-value">{{ $subscription['plan']['name'] ?? 'Unknown plan' }} · {{ ucfirst($subscription['billingPeriod']) }}</span>
                    </div>
                    <div class="bl-row">
                        <span class="bl-label">Status</span>
                        <span class="bl-badge bl-badge-{{ $status ?? 'none' }}">{{ $status ? str_replace('_', ' ', $status) : 'Unknown' }}</span>
                    </div>
                    <div class="bl-row">
                        <span class="bl-label">{{ $status === 'canceled' ? 'Access ends' : 'Renews' }}</span>
                        <span class="bl-value">{{ $subscribedUntil?->format('d M Y') ?? '—' }}</span>
                    </div>

                    @if ($status === 'canceled')
                        <p class="bl-note">Your subscription is cancelled. You'll keep access until the date above, then it won't renew.</p>
                    @elseif ($status === 'active' || $status === 'trialing')
                        <form method="POST" action="{{ route('billing.cancel') }}" onsubmit="return confirm('Cancel your subscription? You\'ll keep access until the end of the current billing period.');">
                            @csrf
                            <button type="submit" class="bl-btn">Cancel subscription</button>
                        </form>
                        <p class="bl-note">Cancelling stops future renewals — you keep full access until the date above.</p>
                    @endif
                @else
                    <div class="bl-row">
                        <span class="bl-label">Plan</span>
                        <span class="bl-value">No active subscription</span>
                    </div>
                    <a href="{{ route('pricing') }}" class="bl-btn-primary">See plans</a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
