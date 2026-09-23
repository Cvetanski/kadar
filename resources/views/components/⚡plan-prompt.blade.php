<?php

use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public bool $dismissed = false;

    public string $role = 'creator';

    public function mount(): void
    {
        $user = Auth::user();

        $this->dismissed = $user->plan_prompt_dismissed || $user->hasActiveSubscription();
        $this->role = $user->role;
    }

    public function dismiss(): void
    {
        Auth::user()->update(['plan_prompt_dismissed' => true]);

        $this->dismissed = true;
    }

    #[Computed]
    public function plan(): array
    {
        return SubscriptionController::PLANS[$this->role] ?? SubscriptionController::PLANS['creator'];
    }

    #[Computed]
    public function freeFeatures(): array
    {
        return $this->role === 'creator'
            ? [
                'Full portfolio, always visible',
                'Browse every open project',
                '2 proposals per day',
            ]
            : [
                '1 project (lifetime)',
                'Up to 5 proposals per project',
                'Browse the first 10 creators in search',
            ];
    }
};
?>

<div>
    @unless ($dismissed)
        <style>
            .pp-wrap{position:relative;margin-bottom:28px;border-radius:24px;padding:28px;background:linear-gradient(135deg,#F6F8FD 0%,#EEF3FC 100%);border:1px solid #E3E9F5;}
            .pp-close{position:absolute;top:16px;right:16px;width:30px;height:30px;border-radius:999px;display:flex;align-items:center;justify-content:center;color:#9AA0AB;background:#fff;border:1px solid #E8EBF0;transition:all .15s ease;z-index:2;}
            .pp-close:hover{color:#4B5563;background:#F6F8FB;}
            .pp-heading{font-size:17px;font-weight:800;color:#14171F;letter-spacing:-0.01em;margin-bottom:4px;}
            .pp-sub{font-size:13px;color:#666B76;margin-bottom:22px;}
            .pp-grid{display:grid;grid-template-columns:1fr;gap:18px;}
            @media (min-width:768px){ .pp-grid{grid-template-columns:1fr 1fr;} }
            .pp-card{position:relative;border-radius:18px;padding:26px 24px;overflow:hidden;transition:transform .2s ease,box-shadow .2s ease;}
            .pp-card-free{background:#fff;border:1px solid #E8EBF0;box-shadow:0 1px 2px rgba(20,23,31,.04);}
            .pp-card-free:hover{transform:translateY(-2px);box-shadow:0 14px 28px rgba(20,23,31,.06);}
            .pp-current-badge{position:absolute;top:18px;right:20px;background:transparent;color:#D6249F;font-size:9px;font-weight:800;letter-spacing:.06em;padding:3px 11px;border-radius:999px;text-transform:uppercase;border:1px solid #D6249F;}
            .pp-card-pro{color:#fff;background:linear-gradient(135deg,#2D82E8,#0958B5);box-shadow:0 10px 28px rgba(11,111,224,.28);}
            .pp-card-pro:hover{transform:translateY(-2px);box-shadow:0 18px 36px rgba(11,111,224,.36);}
            .pp-ribbon{position:absolute;top:16px;right:-32px;transform:rotate(45deg);background:#0B2A57;color:#fff;font-size:9px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;padding:4px 40px;box-shadow:0 2px 6px rgba(0,0,0,.15);}
            .pp-plan-name{font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;}
            .pp-card-free .pp-plan-name{color:#9AA0AB;}
            .pp-card-pro .pp-plan-name{color:#BFE0FF;}
            .pp-price{font-size:30px;font-weight:800;letter-spacing:-0.02em;line-height:1;margin-bottom:18px;}
            .pp-card-free .pp-price{color:#14171F;}
            .pp-price span{font-size:13px;font-weight:600;opacity:.7;}
            .pp-features{list-style:none;margin:0 0 22px;padding:0;}
            .pp-features li{display:flex;align-items:flex-start;gap:10px;font-size:13.5px;padding:7px 0;}
            .pp-card-free .pp-features li{color:#333A44;}
            .pp-check{flex-shrink:0;width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-top:1px;}
            .pp-card-free .pp-check{background:#EEF1F5;color:#9AA0AB;}
            .pp-card-pro .pp-check{background:rgba(255,255,255,.2);}
            .pp-btn{display:block;width:100%;text-align:center;border-radius:12px;padding:12px 16px;font-weight:700;font-size:13.5px;transition:all .15s ease;text-decoration:none;}
            .pp-btn-outline{background:#fff;color:#333A44;border:1px solid #E0E4EB;}
            .pp-btn-outline:hover{background:#F6F8FB;color:#333A44;}
            .pp-btn-solid{background:#fff;color:#0958B5;border:none;box-shadow:0 4px 12px rgba(0,0,0,.12);}
            .pp-btn-solid:hover{background:#F0F7FF;color:#0958B5;transform:translateY(-1px);}
        </style>

        <div class="pp-wrap">
            <button
                type="button"
                wire:click="dismiss"
                class="pp-close"
                aria-label="Close"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <p class="pp-heading pr-10">🚀 Choose a plan</p>
            <p class="pp-sub">You're on the Free plan. Upgrade to Pro to remove all limits.</p>

            <div class="pp-grid">
                <div class="pp-card pp-card-free">
                    <span class="pp-current-badge">Current plan</span>
                    <p class="pp-plan-name">Free</p>
                    <p class="pp-price">$0</p>
                    <ul class="pp-features">
                        @foreach ($this->freeFeatures as $feature)
                            <li>
                                <span class="pp-check">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width:10px;height:10px;"><path d="M20 6L9 17l-5-5"/></svg>
                                </span>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                    <button type="button" wire:click="dismiss" class="pp-btn pp-btn-outline">
                        Get Started
                    </button>
                </div>

                <div class="pp-card pp-card-pro">
                    <span class="pp-ribbon">Pro</span>
                    <p class="pp-plan-name">Pro</p>
                    <p class="pp-price">${{ $this->plan['monthly'] }}<span>/mo</span></p>
                    <ul class="pp-features">
                        @foreach ($this->plan['features'] as $feature)
                            <li>
                                <span class="pp-check">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width:10px;height:10px;"><path d="M20 6L9 17l-5-5"/></svg>
                                </span>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('pricing') }}" class="pp-btn pp-btn-solid">
                        Upgrade to Pro →
                    </a>
                </div>
            </div>
        </div>
    @endunless
</div>
