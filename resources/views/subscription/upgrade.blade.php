<x-app-layout :title="'Subscribe'" description="Subscribe to continue using CreatorSpot.">
    <div class="py-16">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <style>
                .up-card{background:#fff;border:1px solid #E8EBF0;border-radius:20px;padding:40px 32px;text-align:center;}
                .up-icon{
                    width:56px;height:56px;border-radius:16px;margin:0 auto 20px;display:flex;align-items:center;
                    justify-content:center;font-size:26px;background:linear-gradient(135deg,#2D82E8,#0958B5);
                }
                .up-card h1{font-size:22px;font-weight:800;color:#14171F;letter-spacing:-0.01em;margin-bottom:10px;}
                .up-card p{font-size:14.5px;color:#666B76;line-height:1.6;margin-bottom:28px;}
                .up-features{list-style:none;margin:0 0 28px;padding:0;text-align:left;display:inline-block;}
                .up-features li{display:flex;align-items:flex-start;gap:8px;font-size:13.5px;color:#444A54;padding:6px 0;}
                .up-features li:before{content:'✓';color:#17A673;font-weight:800;flex-shrink:0;}
                .up-btn{
                    display:inline-block;background:linear-gradient(135deg,#2D82E8,#0958B5);color:#fff;border:none;
                    border-radius:12px;padding:13px 28px;font-weight:700;font-size:14.5px;font-family:'Inter',sans-serif;
                    text-decoration:none;transition:transform .15s ease;
                }
                .up-btn:hover{transform:translateY(-1px);color:#fff;}
            </style>

            <div class="up-card">
                <div class="up-icon">🔒</div>
                <h1>Subscribe to continue</h1>
                <p>This part of CreatorSpot is available to subscribed members. Pick a plan to unlock full access.</p>

                <ul class="up-features">
                    <li>Unlimited messaging</li>
                    <li>Full search & browsing</li>
                    <li>Cancel anytime</li>
                </ul>

                <div>
                    <a href="{{ route('pricing') }}" class="up-btn">See plans</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
