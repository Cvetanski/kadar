<x-app-layout :title="'Create a project'" description="Upgrade to Pro to post more projects on CreatorSpot.">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Креирај Оглас') }}</h2>
    </x-slot>

    <div class="py-16">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <style>
                .lr-card{background:#fff;border:1px solid #E8EBF0;border-radius:20px;padding:40px 32px;text-align:center;}
                .lr-icon{
                    width:56px;height:56px;border-radius:16px;margin:0 auto 20px;display:flex;align-items:center;
                    justify-content:center;font-size:26px;background:#F6F8FB;border:1px solid #E8EBF0;
                }
                .lr-card h1{font-size:20px;font-weight:800;color:#14171F;letter-spacing:-0.01em;margin-bottom:10px;}
                .lr-card p{font-size:14.5px;color:#666B76;line-height:1.6;margin-bottom:24px;}
                .lr-btn-primary{
                    display:inline-block;background:linear-gradient(135deg,#2D82E8,#0958B5);color:#fff;border:none;
                    border-radius:12px;padding:12px 24px;font-weight:700;font-size:13.5px;font-family:'Inter',sans-serif;
                    text-decoration:none;margin:4px;
                }
                .lr-btn{
                    display:inline-block;background:#fff;color:#14171F;border:1px solid #E8EBF0;
                    border-radius:12px;padding:12px 24px;font-weight:700;font-size:13.5px;font-family:'Inter',sans-serif;
                    text-decoration:none;margin:4px;
                }
            </style>

            <div class="lr-card">
                <div class="lr-icon">🔒</div>
                <h1>{{ __('Го искористи бесплатниот лимит') }}</h1>
                <p>{{ __('Бесплатниот план дозволува само 1 објавен проект. Надогради на Pro за неограничени проекти.') }}</p>
                <div>
                    <a href="{{ route('pricing') }}" class="lr-btn-primary">{{ __('Надогради на Pro →') }}</a>
                    <a href="{{ route('projects.index') }}" class="lr-btn">{{ __('Мои Огласи') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
