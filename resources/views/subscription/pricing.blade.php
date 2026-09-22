<x-app-layout :title="'Pricing'" description="Simple, transparent pricing for creators and clients on CreatorSpot.">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pricing</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <style>
                .pr-intro{text-align:center;max-width:560px;margin:0 auto 36px;}
                .pr-intro h1{font-size:30px;font-weight:800;color:#14171F;letter-spacing:-0.01em;margin-bottom:10px;}
                .pr-intro p{font-size:15px;color:#666B76;line-height:1.6;}

                .pr-toggle{display:flex;justify-content:center;margin-bottom:40px;}
                .pr-toggle-track{display:inline-flex;background:#fff;border:1px solid #E8EBF0;border-radius:999px;padding:4px;gap:2px;
                    box-shadow:0 1px 2px rgba(20,23,31,.05);}
                .pr-toggle-btn{
                    border:none;background:transparent;padding:9px 20px;border-radius:999px;font-family:'Inter',sans-serif;
                    font-weight:700;font-size:13.5px;color:#666B76;cursor:pointer;transition:all .15s ease;
                }
                .pr-toggle-btn.is-active{background:linear-gradient(135deg,#2D82E8,#0958B5);color:#fff;box-shadow:0 4px 14px rgba(11,111,224,.35);}
                .pr-save-badge{
                    display:inline-block;margin-left:8px;background:#E7F8F1;color:#17A673;font-size:10.5px;font-weight:800;
                    letter-spacing:.02em;padding:2px 8px;border-radius:999px;vertical-align:middle;
                }

                .pr-grid{display:grid;grid-template-columns:1fr;gap:22px;max-width:900px;margin:0 auto;}
                @media (min-width:768px){ .pr-grid.pr-grid-2{grid-template-columns:1fr 1fr;} }
                .pr-grid.pr-grid-1{max-width:420px;}

                .pr-card{
                    position:relative;background:#fff;border:1px solid #E8EBF0;border-radius:22px;padding:36px 30px 30px;
                    text-align:center;overflow:hidden;box-shadow:0 1px 2px rgba(20,23,31,.04);transition:transform .2s ease,box-shadow .2s ease;
                }
                .pr-card:hover{transform:translateY(-3px);box-shadow:0 20px 40px rgba(20,23,31,.08),0 4px 12px rgba(20,23,31,.05);}
                .pr-card:before{
                    content:'';position:absolute;top:0;left:0;right:0;height:5px;
                    background:linear-gradient(90deg,#2D82E8,#17A673);
                }
                .pr-card-name{font-size:13px;font-weight:800;color:#0958B5;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;}
                .pr-card-tagline{font-size:13px;color:#9AA0AB;margin-bottom:22px;line-height:1.5;min-height:38px;}
                .pr-card-price{font-size:44px;font-weight:800;color:#14171F;letter-spacing:-0.02em;line-height:1;}
                .pr-card-price span{font-size:15px;font-weight:600;color:#9AA0AB;}
                .pr-card-sub{font-size:12.5px;color:#9AA0AB;margin:6px 0 26px;}
                .pr-card-features{list-style:none;margin:0 0 30px;padding:0;text-align:left;}
                .pr-card-features li{display:flex;align-items:flex-start;gap:10px;font-size:13.5px;color:#333A44;padding:9px 0;
                    border-bottom:1px solid #F1F3F6;}
                .pr-card-features li:last-child{border-bottom:none;}
                .pr-check{
                    flex-shrink:0;width:18px;height:18px;border-radius:50%;background:#0B6FE0;
                    display:flex;align-items:center;justify-content:center;margin-top:1px;
                }
                .pr-check svg{width:10px;height:10px;}

                .pr-card-btn{
                    display:block;width:100%;text-align:center;background:linear-gradient(135deg,#2D82E8,#0958B5);
                    color:#fff;border:none;border-radius:14px;padding:15px 16px;font-weight:700;font-size:15px;
                    font-family:'Inter',sans-serif;text-decoration:none;transition:transform .15s ease,box-shadow .15s ease;
                    box-shadow:0 6px 18px rgba(11,111,224,.28);
                }
                .pr-card-btn:hover{transform:translateY(-1px);color:#fff;box-shadow:0 10px 24px rgba(11,111,224,.38);}
                .pr-card-reassure{font-size:11.5px;color:#9AA0AB;margin-top:14px;}
            </style>

            <div x-data="{ period: 'monthly' }">
                <div class="pr-intro">
                    <h1>Simple, transparent pricing</h1>
                    <p>{{ count($plans) === 1 ? "Here's what's included on your plan." : 'One plan for creators, one for clients. Cancel anytime.' }}</p>
                </div>

                <div class="pr-toggle">
                    <div class="pr-toggle-track">
                        <button type="button" class="pr-toggle-btn" :class="period === 'monthly' ? 'is-active' : ''" @click="period = 'monthly'">
                            Monthly
                        </button>
                        <button type="button" class="pr-toggle-btn" :class="period === 'annual' ? 'is-active' : ''" @click="period = 'annual'">
                            Annual <span class="pr-save-badge" x-show="period === 'annual'">Save 17%</span>
                        </button>
                    </div>
                </div>

                <div class="pr-grid {{ count($plans) === 1 ? 'pr-grid-1' : 'pr-grid-2' }}">
                    @foreach ($plans as $planKey => $plan)
                        <div class="pr-card">
                            <div class="pr-card-name">{{ $plan['name'] }}</div>
                            <p class="pr-card-tagline">{{ $plan['tagline'] }}</p>

                            <div x-show="period === 'monthly'">
                                <div class="pr-card-price">${{ $plan['monthly'] }}<span>/mo</span></div>
                                <p class="pr-card-sub">Billed monthly</p>
                            </div>
                            <div x-show="period === 'annual'" style="display:none;">
                                <div class="pr-card-price">${{ $plan['annual'] }}<span>/yr</span></div>
                                <p class="pr-card-sub">Billed annually — ${{ number_format($plan['annual'] / 12, 2) }}/mo</p>
                            </div>

                            <ul class="pr-card-features">
                                @foreach ($plan['features'] as $feature)
                                    <li>
                                        <span class="pr-check">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                                        </span>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <a href="{{ route('checkout.show', $planKey.'-monthly') }}" class="pr-card-btn" x-show="period === 'monthly'">
                                Continue →
                            </a>
                            <a href="{{ route('checkout.show', $planKey.'-annual') }}" class="pr-card-btn" x-show="period === 'annual'" style="display:none;">
                                Continue →
                            </a>
                            <p class="pr-card-reassure">No setup fees · Cancel anytime</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
