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

    /**
     * When true, the component starts in read-only preview mode (real
     * embeds, same as a visitor sees) with an "Уреди портфолио" toggle to
     * switch into the add/remove editor — used on the public profile page.
     * When false, it always shows the editor directly — used on the
     * dedicated edit-profile pages, which are already an "editing" context.
     */
    public bool $collapsible = false;

    public bool $editing = false;

    /**
     * Bumped after every reorder so the item list's wire:key changes,
     * forcing Livewire to fully tear down and rebuild that DOM subtree
     * instead of morphing it in place. SortableJS moves the actual DOM
     * nodes itself during a drag, which leaves the subsequent morph with
     * a structure it didn't expect — without this, embeds can render blank
     * until a manual refresh, and the sortable container can stop
     * responding to drags after a reorder.
     */
    public int $renderKey = 0;

    public string $newTitle = '';

    public string $newType = 'video';

    public string $newUrl = '';

    public function mount(CreatorProfile $creatorProfile, bool $collapsible = false): void
    {
        $this->authorizeManaging($creatorProfile);

        $this->creatorProfile = $creatorProfile;
        $this->collapsible = $collapsible;
        $this->editing = ! $collapsible;
    }

    private function authorizeManaging(CreatorProfile $creatorProfile): void
    {
        abort_unless(
            Auth::id() === $creatorProfile->user_id || Auth::user()?->is_admin,
            403
        );
    }

    public function toggleEditing(): void
    {
        $this->editing = ! $this->editing;
    }

    #[Computed]
    public function items()
    {
        return $this->creatorProfile->portfolioItems()->get();
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

    public function reorder($itemId, $position): void
    {
        $this->authorizeManaging($this->creatorProfile);

        $itemId = (int) $itemId;
        $position = (int) $position;

        $items = $this->creatorProfile->portfolioItems()->get();
        $moved = $items->firstWhere('id', $itemId);

        abort_unless($moved, 404);

        $items = $items->reject(fn ($item) => $item->id === $itemId)->values();
        $items->splice($position, 0, [$moved]);

        foreach ($items as $index => $item) {
            if ($item->sort_order !== $index) {
                $item->update(['sort_order' => $index]);
            }
        }

        $this->renderKey++;
        unset($this->items);
    }
};
?>

<div class="kf-card" @unless ($collapsible) style="margin-bottom:20px;" @endunless>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <p @if ($collapsible) class="text-lg font-medium text-gray-900" style="margin:0;" @else class="kf-card-title" style="margin:0;" @endif>
            {{ __('Портфолио') }}
        </p>

        @if ($collapsible)
            <button type="button" wire:click="toggleEditing"
                class="inline-flex items-center gap-1.5 rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition"
                wire:loading.attr="disabled" wire:target="toggleEditing">
                {{ $editing ? __('Готово') : '✏️ '.__('Уреди портфолио') }}
            </button>
        @endif
    </div>

    @if (! $editing)
        @if ($this->items->isEmpty())
            <p class="text-sm text-gray-500">{{ __('Сеуште нема додадено ставки') }}</p>
        @else
            <div wire:key="portfolio-preview-list-{{ $renderKey }}" wire:sort="reorder" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach ($this->items as $item)
                    <div wire:key="portfolio-preview-{{ $item->id }}-{{ $renderKey }}" wire:sort:item="{{ $item->id }}">
                        <div wire:sort:handle style="display:flex;align-items:center;gap:6px;margin-bottom:8px;cursor:grab;user-select:none;">
                            <span style="color:#9AA0AB;font-size:13px;line-height:1;">⠿</span>
                            @if ($item->title)
                                <p class="text-sm font-medium text-gray-900" style="margin:0;">{{ $item->title }}</p>
                            @endif
                        </div>
                        <x-portfolio-preview :item="$item" />
                    </div>
                @endforeach
            </div>
        @endif
    @else
        @if ($this->items->isNotEmpty())
            <div wire:key="portfolio-edit-list-{{ $renderKey }}" wire:sort="reorder" style="display:flex;flex-direction:column;gap:10px;margin-bottom:18px;">
                @foreach ($this->items as $item)
                    <div wire:key="portfolio-item-{{ $item->id }}-{{ $renderKey }}" wire:sort:item="{{ $item->id }}" style="display:flex;align-items:center;justify-content:space-between;gap:12px;border:1px solid #E8EBF0;border-radius:10px;padding:10px 14px;cursor:grab;user-select:none;">
                        <div style="display:flex;align-items:center;gap:8px;min-width:0;">
                            <span style="color:#9AA0AB;font-size:13px;line-height:1;flex-shrink:0;">⠿</span>
                            <div style="min-width:0;">
                                <p style="font-weight:700;font-size:13.5px;color:#14171F;margin:0;">{{ $item->title }}</p>
                                <p style="font-size:12px;color:#9AA0AB;margin:2px 0 0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $item->media_url }}</p>
                            </div>
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
                    <label style="display:block;font-size:13px;font-weight:700;color:#14171F;margin-bottom:6px;">{{ __('Наслов') }}</label>
                    <input type="text" wire:model="newTitle" wire:keydown.enter.prevent="addItem" placeholder="{{ __('Свадба - Марија и Стефан') }}"
                        style="width:100%;padding:11px 14px;border:1px solid #E8EBF0;border-radius:10px;font-family:'Inter',sans-serif;font-size:14.5px;background:#F6F8FB;">
                </div>
                <div class="kf-field">
                    <label style="display:block;font-size:13px;font-weight:700;color:#14171F;margin-bottom:6px;">{{ __('Тип') }}</label>
                    <select wire:model="newType" style="width:100%;padding:11px 14px;border:1px solid #E8EBF0;border-radius:10px;font-family:'Inter',sans-serif;font-size:14.5px;background:#F6F8FB;">
                        <option value="video">{{ __('Видео') }}</option>
                        <option value="image">{{ __('Слика') }}</option>
                    </select>
                </div>
            </div>
            <div class="kf-field">
                <label style="display:block;font-size:13px;font-weight:700;color:#14171F;margin-bottom:6px;">{{ __('Линк') }}</label>
                <input type="url" wire:model="newUrl" wire:keydown.enter.prevent="addItem" placeholder="https://..."
                    style="width:100%;padding:11px 14px;border:1px solid #E8EBF0;border-radius:10px;font-family:'Inter',sans-serif;font-size:14.5px;background:#F6F8FB;">
                <x-input-error :messages="$errors->get('newUrl')" class="mt-2" />
                <x-input-error :messages="$errors->get('newType')" class="mt-2" />
            </div>
            <button type="button" wire:click="addItem" class="kf-btn">{{ __('Додади') }}</button>
        </div>
    @endif
</div>
