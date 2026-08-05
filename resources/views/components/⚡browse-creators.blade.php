<?php

use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\CreatorProfile;
use App\Models\Skill;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(as: 'category_ids', history: true)]
    public array $categoryIds = [];

    #[Url(history: true)]
    public array $skillIds = [];

    #[Url(history: true)]
    public ?int $countryId = null;

    #[Url(history: true)]
    public ?int $cityId = null;

    #[Url(history: true)]
    public ?string $priceMin = null;

    #[Url(history: true)]
    public ?string $priceMax = null;

    #[Url(history: true)]
    public bool $remoteOnly = false;

    #[Url(history: true)]
    public string $sortBy = 'latest';

    public ?int $selectedCreatorId = null;

    public bool $showFilters = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryIds(): void
    {
        $this->resetPage();
    }

    public function updatingSkillIds(): void
    {
        $this->resetPage();
    }

    public function updatingCountryId(): void
    {
        $this->cityId = null;
        $this->resetPage();
    }

    public function updatingCityId(): void
    {
        $this->resetPage();
    }

    public function updatingPriceMin(): void
    {
        $this->resetPage();
    }

    public function updatingPriceMax(): void
    {
        $this->resetPage();
    }

    public function updatingRemoteOnly(): void
    {
        $this->resetPage();
    }

    public function updatingSortBy(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'categoryIds', 'skillIds', 'countryId', 'cityId', 'priceMin', 'priceMax', 'remoteOnly', 'sortBy']);
        $this->resetPage();
    }

    public function toggleCategory(int $categoryId): void
    {
        if (in_array($categoryId, $this->categoryIds, true)) {
            $this->categoryIds = array_values(array_diff($this->categoryIds, [$categoryId]));
        } else {
            $this->categoryIds[] = $categoryId;
        }

        // Drop skill selections that no longer belong to a selected category.
        if ($this->skillIds !== []) {
            $validSkillIds = Skill::whereHas('categories', fn ($q) => $q->whereIn('categories.id', $this->categoryIds))
                ->pluck('id')
                ->all();
            $this->skillIds = array_values(array_intersect($this->skillIds, $validSkillIds));
        }

        $this->resetPage();
    }

    public function clearCategories(): void
    {
        $this->categoryIds = [];
        $this->skillIds = [];
        $this->resetPage();
    }

    public function toggleSkill(int $skillId): void
    {
        if (in_array($skillId, $this->skillIds, true)) {
            $this->skillIds = array_values(array_diff($this->skillIds, [$skillId]));
        } else {
            $this->skillIds[] = $skillId;
        }

        $this->resetPage();
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('slug')->get();
    }

    #[Computed]
    public function skillsByCategory()
    {
        if ($this->categoryIds === []) {
            return collect();
        }

        return Category::whereIn('id', $this->categoryIds)
            ->with('skills')
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn ($category) => [$category->id => $category->skills])
            ->filter(fn ($skills) => $skills->isNotEmpty());
    }

    #[Computed]
    public function countries()
    {
        return Country::orderBy('id')->get();
    }

    #[Computed]
    public function cities()
    {
        if (! $this->countryId) {
            return collect();
        }

        return City::where('country_id', $this->countryId)->get()->sortBy('name');
    }

    #[Computed]
    public function creators()
    {
        $query = CreatorProfile::whereNotNull('onboarding_completed_at')
            ->with(['user.country', 'user.city', 'categories'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        if ($this->categoryIds !== []) {
            $query->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $this->categoryIds));
        }

        if ($this->skillIds !== []) {
            $query->whereHas('skills', fn ($q) => $q->whereIn('skills.id', $this->skillIds));
        }

        if ($this->countryId) {
            $query->whereHas('user', fn ($q) => $q->where('country_id', $this->countryId));
        }

        if ($this->cityId) {
            $query->whereHas('user', fn ($q) => $q->where('city_id', $this->cityId));
        }

        if ($this->priceMin !== null && $this->priceMin !== '') {
            $query->where('hourly_rate', '>=', $this->priceMin);
        }

        if ($this->priceMax !== null && $this->priceMax !== '') {
            $query->where('hourly_rate', '<=', $this->priceMax);
        }

        if ($this->remoteOnly) {
            $query->where('remote_ok', true);
        }

        if ($this->search !== '') {
            $needle = '%'.$this->search.'%';
            $query->where(function ($q) use ($needle) {
                $q->where('headline', 'like', $needle)
                    ->orWhere('bio', 'like', $needle)
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', $needle));
            });
        }

        if ($this->sortBy === 'rating') {
            $query->orderByDesc('reviews_avg_rating')->orderByDesc('reviews_count');
        } else {
            $query->latest();
        }

        return $query->paginate(10);
    }

    #[Computed]
    public function selectedCreator()
    {
        if (! $this->selectedCreatorId) {
            return null;
        }

        return CreatorProfile::with(['user.country', 'user.city', 'categories', 'skills'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->find($this->selectedCreatorId);
    }

    #[Computed]
    public function isSelectedFavorited(): bool
    {
        if (! $this->selectedCreatorId) {
            return false;
        }

        return Auth::user()->favorites()->where('creator_profile_id', $this->selectedCreatorId)->exists();
    }

    public function selectCreator(int $creatorProfileId): void
    {
        $this->selectedCreatorId = $creatorProfileId;
        unset($this->selectedCreator, $this->isSelectedFavorited);
    }

    public function toggleFavorite(): void
    {
        if (! $this->selectedCreatorId) {
            return;
        }

        Auth::user()->favorites()->toggle($this->selectedCreatorId);

        unset($this->isSelectedFavorited);
    }
}; ?>

<div>
    <div class="br-toolbar">
        <div class="br-searchbox">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="{{ __('Пребарај креативци по име или опис...') }}">
        </div>
        <button type="button" class="br-filters-toggle" wire:click="$toggle('showFilters')">
            {{ __('Филтри') }}
        </button>
    </div>

    <div class="br-chips">
        <button type="button" class="br-chip {{ $categoryIds === [] ? 'is-active' : '' }}" wire:click="clearCategories">
            {{ __('Сите') }}
        </button>
        @foreach ($this->categories as $category)
            <button type="button" wire:key="category-chip-{{ $category->id }}" class="br-chip {{ in_array($category->id, $categoryIds, true) ? 'is-active' : '' }}" wire:click="toggleCategory({{ $category->id }})">
                {{ $category->icon }} {{ $category->name }}
            </button>
        @endforeach
    </div>

    @if ($this->skillsByCategory->isNotEmpty())
        <div class="br-chips" style="margin-top:-10px;">
            @foreach ($this->skillsByCategory->flatten()->unique('id') as $skill)
                <button type="button" wire:key="skill-chip-{{ $skill->id }}"
                    class="br-chip br-chip-sm {{ in_array($skill->id, $skillIds, true) ? 'is-active' : '' }}"
                    wire:click="toggleSkill({{ $skill->id }})">
                    {{ $skill->name }}
                </button>
            @endforeach
        </div>
    @endif

    <div class="br-layout">
        <aside class="br-filters {{ $showFilters ? 'is-open' : '' }}">
            <div class="br-filters-head">
                <span>{{ __('Филтри') }}</span>
                @if ($search !== '' || $categoryIds !== [] || $skillIds !== [] || $countryId || $cityId || $priceMin || $priceMax || $remoteOnly || $sortBy !== 'latest')
                    <button type="button" class="br-reset" wire:click="resetFilters">{{ __('Ресетирај') }}</button>
                @endif
            </div>

            <div class="br-filter-group">
                <label>{{ __('Сортирај по') }}</label>
                <select wire:model.live="sortBy">
                    <option value="latest">{{ __('Најнови') }}</option>
                    <option value="rating">{{ __('Најдобар рејтинг') }}</option>
                </select>
            </div>

            <div class="br-filter-group">
                <label>{{ __('Земја') }}</label>
                <select wire:model.live="countryId">
                    <option value="">{{ __('Сите земји') }}</option>
                    @foreach ($this->countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>

            @if ($countryId)
                <div class="br-filter-group">
                    <label>{{ __('Град') }}</label>
                    <select wire:model.live="cityId">
                        <option value="">{{ __('Сите градови') }}</option>
                        @foreach ($this->cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="br-filter-group">
                <label>{{ __('Цена (EUR/ч)') }}</label>
                <div class="br-budget-row">
                    <input type="number" min="0" wire:model.live.debounce.500ms="priceMin" placeholder="{{ __('Од') }}">
                    <span>–</span>
                    <input type="number" min="0" wire:model.live.debounce.500ms="priceMax" placeholder="{{ __('До') }}">
                </div>
            </div>

            <label class="br-toggle">
                <span>{{ __('Само Remote') }}</span>
                <span class="br-switch">
                    <input type="checkbox" wire:model.live="remoteOnly">
                    <span class="br-switch-track"></span>
                </span>
            </label>
        </aside>

        <section class="br-list">
            <p class="br-count">{{ __('Најдени :count креативци', ['count' => $this->creators->total()]) }}</p>

            @if ($this->creators->isEmpty())
                <div class="br-empty">{{ __('Нема креативци што одговараат на филтрите.') }}</div>
            @else
                <div class="br-rows">
                    @foreach ($this->creators as $creator)
                        <button type="button" wire:key="creator-row-{{ $creator->id }}" wire:click="selectCreator({{ $creator->id }})"
                            class="br-row {{ $selectedCreatorId === $creator->id ? 'is-active' : '' }}">
                            <div class="br-row-person">
                                <x-avatar :user="$creator->user" size="w-11 h-11" textSize="text-sm" />
                                <div class="br-row-person-info">
                                    <p class="br-row-title">
                                        {{ $creator->user->name }}
                                        @if ($creator->verified)
                                            <span class="br-verified">
                                                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="12"/><path d="M7.5 12.5l3 3 6-6.5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>
                                                {{ __('Верифициран') }}
                                            </span>
                                        @endif
                                    </p>
                                    <p class="br-row-cats">{{ $creator->headline }}</p>
                                </div>
                            </div>
                            <p class="br-row-cats-bold">{{ $creator->categories->pluck('name')->join(', ') }}</p>
                            @if ($creator->bio)
                                <p class="br-row-desc">{{ \Illuminate\Support\Str::limit($creator->bio, 110) }}</p>
                            @endif
                            <div class="br-row-foot">
                                <x-star-rating :rating="$creator->reviews_avg_rating" :count="$creator->reviews_count" size="text-xs" />
                                <span class="br-row-location">📍 {{ $creator->remote_ok ? __('Remote') : ($creator->user->city?->name ?? $creator->user->country?->name ?? '—') }}</span>
                                <span class="br-budget" style="margin-left:auto;">{{ $creator->hourly_rate ? $creator->hourly_rate.' EUR/ч' : '—' }}</span>
                            </div>
                        </button>
                    @endforeach
                </div>

                <div class="br-pagination">{{ $this->creators->links() }}</div>
            @endif
        </section>

        <section class="br-details">
            @if (! $this->selectedCreator)
                <div class="br-details-empty">{{ __('Избери креативец од листата за да видиш детали.') }}</div>
            @else
                @php($creator = $this->selectedCreator)
                <div class="br-details-person">
                    <x-avatar :user="$creator->user" size="w-14 h-14" textSize="text-lg" />
                    <div class="br-details-person-info">
                        <h2>
                            {{ $creator->user->name }}
                            @if ($creator->verified)
                                <span class="br-verified">
                                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="12"/><path d="M7.5 12.5l3 3 6-6.5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>
                                    {{ __('Верифициран') }}
                                </span>
                            @endif
                        </h2>
                    </div>
                </div>
                <p class="br-details-posted">{{ $creator->headline }}</p>

                <div class="br-details-budget">
                    {{ $creator->hourly_rate ? $creator->hourly_rate.' EUR/ч' : ($creator->project_rate_from ? __('Од').' '.$creator->project_rate_from.' EUR' : '—') }}
                </div>

                <div class="br-details-meta">
                    <span><x-star-rating :rating="$creator->reviews_avg_rating" :count="$creator->reviews_count" size="text-xs" /></span>
                    <span>📍 {{ $creator->remote_ok ? __('Remote') : ($creator->user->city?->name ?? $creator->user->country?->name ?? '—') }}</span>
                    @if ($creator->experience_years)
                        <span>🎓 {{ __(':count години искуство', ['count' => $creator->experience_years]) }}</span>
                    @endif
                </div>

                <div class="br-pill-row">
                    @foreach ($creator->categories as $category)
                        <span class="kf-pill">{{ $category->icon }} {{ $category->name }}</span>
                    @endforeach
                </div>

                @if ($creator->skills->isNotEmpty())
                    <h3 class="br-section-title">{{ __('Вештини') }}</h3>
                    <div class="br-pill-row">
                        @foreach ($creator->skills as $skill)
                            <span class="kf-pill">{{ $skill->name }}</span>
                        @endforeach
                    </div>
                @endif

                @if ($creator->bio)
                    <h3 class="br-section-title">{{ __('За креативецот') }}</h3>
                    <p class="br-details-desc">{{ $creator->bio }}</p>
                @endif

                <div class="br-actions">
                    <a href="{{ route('creators.show', $creator) }}" class="br-btn-primary">
                        {{ __('Види целосен профил') }}
                    </a>
                    <button type="button" wire:click="toggleFavorite" class="br-btn-bookmark {{ $this->isSelectedFavorited ? 'is-saved' : '' }}" title="{{ __('Зачувај креативец') }}">
                        {{ $this->isSelectedFavorited ? '★' : '☆' }}
                    </button>
                </div>

                <form method="POST" action="{{ route('messages.start', $creator) }}">
                    @csrf
                    <button type="submit" class="br-btn-secondary">{{ __('Испрати порака') }}</button>
                </form>
            @endif
        </section>
    </div>
</div>
