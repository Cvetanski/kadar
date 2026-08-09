<?php

use App\Models\CreatorProfile;
use App\Services\VimeoOembedService;
use App\Support\PortfolioSource;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public CreatorProfile $creatorProfile;

    public string $newTitle = '';

    public string $newType = 'video';

    public string $newUrl = '';

    public function mount(CreatorProfile $creatorProfile): void
    {
        $this->authorizeManaging($creatorProfile);

        $this->creatorProfile = $creatorProfile;
    }

    private function authorizeManaging(CreatorProfile $creatorProfile): void
    {
        abort_unless(
            Auth::id() === $creatorProfile->user_id || Auth::user()?->is_admin,
            403
        );
    }

    #[Computed]
    public function items()
    {
        return $this->creatorProfile->portfolioItems()->orderBy('sort_order')->get();
    }

    public function addItem(): void
    {
        $this->authorizeManaging($this->creatorProfile);

        $this->validate([
            'newUrl' => ['required', 'url', 'max:2048'],
            'newType' => ['required', 'in:video,image'],
        ], [
            'newUrl.required' => __('Внеси линк.'),
            'newUrl.url' => __('Ова не е валиден линк.'),
            'newType.required' => __('Избери тип на содржина.'),
        ]);

        $thumbnailUrl = null;

        if (PortfolioSource::type($this->newUrl) === 'vimeo') {
            $thumbnailUrl = app(VimeoOembedService::class)->thumbnailFor($this->newUrl);
        }

        $this->creatorProfile->portfolioItems()->create([
            'title' => $this->newTitle !== '' ? $this->newTitle : __('Без наслов'),
            'media_type' => $this->newType,
            'media_url' => $this->newUrl,
            'thumbnail_url' => $thumbnailUrl,
            'sort_order' => $this->creatorProfile->portfolioItems()->count(),
        ]);

        $this->newTitle = '';
        $this->newUrl = '';
        $this->resetErrorBag(['newUrl', 'newType']);
        unset($this->items);
    }

    public function removeItem(int $itemId): void
    {
        $this->authorizeManaging($this->creatorProfile);

        $this->creatorProfile->portfolioItems()->where('id', $itemId)->delete();

        unset($this->items);
    }
};
?>

<div class="kf-card" style="margin-bottom:20px;">
    <p class="kf-card-title" style="margin-bottom:16px;">{{ __('Портфолио') }}</p>

    @if ($this->items->isNotEmpty())
        <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:18px;">
            @foreach ($this->items as $item)
                <div wire:key="portfolio-item-{{ $item->id }}" style="display:flex;align-items:center;justify-content:space-between;gap:12px;border:1px solid #E8EBF0;border-radius:10px;padding:10px 14px;">
                    <div style="min-width:0;">
                        <p style="font-weight:700;font-size:13.5px;color:#14171F;margin:0;">{{ $item->title }}</p>
                        <p style="font-size:12px;color:#9AA0AB;margin:2px 0 0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $item->media_url }}</p>
                    </div>
                    <button type="button" wire:click="removeItem({{ $item->id }})" wire:confirm="{{ __('Дали сигурно сакаш да го избришеш ова?') }}"
                        style="flex-shrink:0;color:#DC2626;font-size:13px;font-weight:700;background:none;border:none;cursor:pointer;">
                        ✕ {{ __('Избриши') }}
                    </button>
                </div>
            @endforeach
        </div>
    @else
        <p class="kf-card-meta" style="margin-bottom:16px;">{{ __('Сеуште нема додадено ставки') }}</p>
    @endif

    <div style="border-top:1px solid #E8EBF0;padding-top:16px;">
        <div class="kf-two-col">
            <div class="kf-field">
                <label>{{ __('Наслов') }}</label>
                <input type="text" wire:model="newTitle" wire:keydown.enter.prevent="addItem" placeholder="{{ __('Свадба - Марија и Стефан') }}">
            </div>
            <div class="kf-field">
                <label>{{ __('Тип') }}</label>
                <select wire:model="newType" style="width:100%;padding:11px 14px;border:1px solid #E8EBF0;border-radius:10px;font-family:'Inter',sans-serif;font-size:14.5px;background:#F6F8FB;">
                    <option value="video">{{ __('Видео') }}</option>
                    <option value="image">{{ __('Слика') }}</option>
                </select>
            </div>
        </div>
        <div class="kf-field">
            <label>{{ __('Линк') }}</label>
            <input type="url" wire:model="newUrl" wire:keydown.enter.prevent="addItem" placeholder="https://...">
            <x-input-error :messages="$errors->get('newUrl')" class="mt-2" />
            <x-input-error :messages="$errors->get('newType')" class="mt-2" />
        </div>
        <button type="button" wire:click="addItem" class="kf-btn">{{ __('Додади') }}</button>
    </div>
</div>
