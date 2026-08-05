<?php

use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    /**
     * Below this many received reviews we hide the (potentially thin/weak)
     * numeric client rating and show a "New client" badge instead — same
     * rule as the existing "New" badge for creators without reviews.
     */
    private const MIN_REVIEWS_FOR_RATING = 3;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public array $categoryIds = [];

    #[Url(history: true)]
    public ?int $countryId = null;

    #[Url(history: true)]
    public ?int $cityId = null;

    #[Url(history: true)]
    public ?string $budgetMin = null;

    #[Url(history: true)]
    public ?string $budgetMax = null;

    #[Url(history: true)]
    public bool $remoteOnly = false;

    public ?int $selectedProjectId = null;

    public bool $showFilters = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryIds(): void
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

    public function updatingBudgetMin(): void
    {
        $this->resetPage();
    }

    public function updatingBudgetMax(): void
    {
        $this->resetPage();
    }

    public function updatingRemoteOnly(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'categoryIds', 'countryId', 'cityId', 'budgetMin', 'budgetMax', 'remoteOnly']);
        $this->resetPage();
    }

    public function toggleCategory(int $categoryId): void
    {
        if (in_array($categoryId, $this->categoryIds, true)) {
            $this->categoryIds = array_values(array_diff($this->categoryIds, [$categoryId]));
        } else {
            $this->categoryIds[] = $categoryId;
        }

        $this->resetPage();
    }

    public function clearCategories(): void
    {
        $this->categoryIds = [];
        $this->resetPage();
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('slug')->get();
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
    public function projects()
    {
        $query = Project::where('status', 'open')
            ->with(['categories', 'country', 'city', 'client' => function ($q) {
                $q->withCount('reviewsReceived')->withAvg('reviewsReceived', 'rating');
            }])
            ->withCount('proposals');

        if ($this->categoryIds !== []) {
            $query->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $this->categoryIds));
        }

        if ($this->countryId) {
            $query->where('country_id', $this->countryId);
        }

        if ($this->cityId) {
            $query->where('city_id', $this->cityId);
        }

        if ($this->budgetMin !== null && $this->budgetMin !== '') {
            $query->where(function ($q) {
                $q->where('budget_max', '>=', $this->budgetMin)->orWhereNull('budget_max');
            });
        }

        if ($this->budgetMax !== null && $this->budgetMax !== '') {
            $query->where(function ($q) {
                $q->where('budget_min', '<=', $this->budgetMax)->orWhereNull('budget_min');
            });
        }

        if ($this->remoteOnly) {
            $query->where('remote_ok', true);
        }

        if ($this->search !== '') {
            $needle = '%'.$this->search.'%';
            $query->where(fn ($q) => $q->where('title', 'like', $needle)->orWhere('description', 'like', $needle));
        }

        return $query->latest()->paginate(10);
    }

    #[Computed]
    public function selectedProject()
    {
        if (! $this->selectedProjectId) {
            return null;
        }

        return Project::with(['categories', 'skills', 'country', 'city'])
            ->withCount('proposals')
            ->with(['client' => function ($q) {
                $q->withCount(['reviewsReceived', 'projects'])->withAvg('reviewsReceived', 'rating');
            }])
            ->find($this->selectedProjectId);
    }

    #[Computed]
    public function isSelectedSaved(): bool
    {
        if (! $this->selectedProjectId) {
            return false;
        }

        return Auth::user()->savedProjects()->where('project_id', $this->selectedProjectId)->exists();
    }

    public function selectProject(int $projectId): void
    {
        $this->selectedProjectId = $projectId;
        unset($this->selectedProject, $this->isSelectedSaved);
    }

    public function closeDetails(): void
    {
        $this->selectedProjectId = null;
    }

    public function toggleSave(): void
    {
        if (! $this->selectedProjectId) {
            return;
        }

        $user = Auth::user();

        if ($user->savedProjects()->where('project_id', $this->selectedProjectId)->exists()) {
            $user->savedProjects()->detach($this->selectedProjectId);
        } else {
            $user->savedProjects()->attach($this->selectedProjectId);
        }

        unset($this->isSelectedSaved);
    }

    /**
     * Whether a client has enough review history to show a real rating
     * rather than the "New client" badge.
     */
    public function clientHasRatingHistory($client): bool
    {
        return ($client->reviews_received_count ?? 0) >= self::MIN_REVIEWS_FOR_RATING;
    }
}; ?>

<div>
    <div class="br-toolbar">
        <div class="br-searchbox">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="{{ __('Пребарај огласи по наслов или опис...') }}">
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

    <div class="br-layout">
        <div class="br-filters-backdrop {{ $showFilters ? 'is-open' : '' }}" wire:click="$set('showFilters', false)"></div>

        <aside class="br-filters {{ $showFilters ? 'is-open' : '' }}">
            <div class="br-filters-head">
                <span>{{ __('Филтри') }}</span>
                @if ($search !== '' || $categoryIds !== [] || $countryId || $cityId || $budgetMin || $budgetMax || $remoteOnly)
                    <button type="button" class="br-reset" wire:click="resetFilters">{{ __('Ресетирај') }}</button>
                @endif
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
                <label>{{ __('Буџет (EUR)') }}</label>
                <div class="br-budget-row">
                    <input type="number" min="0" wire:model.live.debounce.500ms="budgetMin" placeholder="{{ __('Од') }}">
                    <span>–</span>
                    <input type="number" min="0" wire:model.live.debounce.500ms="budgetMax" placeholder="{{ __('До') }}">
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
            <p class="br-count">{{ __('Најдени :count огласи', ['count' => $this->projects->total()]) }}</p>

            @if ($this->projects->isEmpty())
                <div class="br-empty">{{ __('Нема отворени огласи што одговараат на филтрите.') }}</div>
            @else
                <div class="br-rows">
                    @foreach ($this->projects as $project)
                        <button type="button" wire:key="project-row-{{ $project->id }}" wire:click="selectProject({{ $project->id }})"
                            class="br-row {{ $selectedProjectId === $project->id ? 'is-active' : '' }}">
                            <div class="br-row-top">
                                <p class="br-row-title">
                                    {{ $project->title }}
                                    @if ($project->created_at->diffInHours(now()) < 48)
                                        <span class="br-badge-new">{{ __('НОВО') }}</span>
                                    @endif
                                </p>
                                <span class="br-row-status kf-status kf-status-open">{{ __('Отворен') }}</span>
                            </div>
                            <p class="br-row-cats">{{ $project->categories->pluck('name')->join(', ') }}</p>
                            <p class="br-row-desc">{{ \Illuminate\Support\Str::limit($project->description, 140) }}</p>
                            <div class="br-row-foot">
                                <span class="br-budget">
                                    @if ($project->budget_min || $project->budget_max)
                                        {{ $project->budget_min ?? '?' }}–{{ $project->budget_max ?? '?' }} EUR
                                    @else
                                        {{ __('Цена по договор') }}
                                    @endif
                                </span>
                                <span class="br-row-location">📍 {{ $project->remote_ok ? __('Remote') : ($project->city?->name ?? $project->country?->name ?? '—') }}</span>
                                <span class="br-row-time">{{ $project->created_at->locale(app()->getLocale())->diffForHumans() }}</span>
                            </div>
                        </button>
                    @endforeach
                </div>

                <div class="br-pagination">{{ $this->projects->links() }}</div>
            @endif
        </section>

        <section class="br-details">
            @if (! $this->selectedProject)
                <div class="br-details-empty">{{ __('Избери оглас од листата за да видиш детали.') }}</div>
            @else
                @php($project = $this->selectedProject)
                <div class="br-details-head">
                    <h2>{{ $project->title }}</h2>
                    <span class="kf-status kf-status-open">{{ __('Отворен') }}</span>
                </div>
                <p class="br-details-posted">{{ __('Објавен :time', ['time' => $project->created_at->locale(app()->getLocale())->diffForHumans()]) }}</p>

                <div class="br-details-budget">
                    @if ($project->budget_min || $project->budget_max)
                        {{ $project->budget_min ?? '?' }}–{{ $project->budget_max ?? '?' }} EUR
                    @else
                        {{ __('Цена по договор') }}
                    @endif
                </div>

                <div class="br-details-meta">
                    <span>📍 {{ $project->remote_ok ? __('Remote') : ($project->city?->name ?? $project->country?->name ?? '—') }}</span>
                    @if ($project->deadline)
                        <span>🗓 {{ __('Рок') }}: {{ $project->deadline->format('d.m.Y') }}</span>
                    @endif
                    <span>📨 {{ __(':count апликации', ['count' => $project->proposals_count]) }}</span>
                </div>

                <div class="br-pill-row">
                    @foreach ($project->categories as $category)
                        <span class="kf-pill">{{ $category->icon }} {{ $category->name }}</span>
                    @endforeach
                </div>

                @if ($project->skills->isNotEmpty())
                    <h3 class="br-section-title">{{ __('Потребни вештини') }}</h3>
                    <div class="br-pill-row">
                        @foreach ($project->skills as $skill)
                            <span class="kf-pill">{{ $skill->name }}</span>
                        @endforeach
                    </div>
                @endif

                <h3 class="br-section-title">{{ __('Опис') }}</h3>
                <p class="br-details-desc">{{ $project->description }}</p>

                <div class="br-actions">
                    <a href="{{ route('projects.show', $project) }}" class="br-btn-primary">
                        {{ __('Аплицирај на проект') }}
                    </a>
                    <button type="button" wire:click="toggleSave" class="br-btn-bookmark {{ $this->isSelectedSaved ? 'is-saved' : '' }}" title="{{ __('Зачувај оглас') }}">
                        {{ $this->isSelectedSaved ? '★' : '☆' }}
                    </button>
                </div>

                <form method="POST" action="{{ route('messages.startWithClient', $project->client) }}">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <button type="submit" class="br-btn-secondary">{{ __('Испрати порака на клиент') }}</button>
                </form>

                <div class="br-client">
                    <x-avatar :user="$project->client" size="w-10 h-10" textSize="text-sm" />
                    <div class="br-client-info">
                        <a href="{{ route('clients.show', $project->client) }}" class="br-client-name">{{ $project->client->name }}</a>
                        @if ($this->clientHasRatingHistory($project->client))
                            <x-star-rating :rating="$project->client->reviews_received_avg_rating" :count="$project->client->reviews_received_count" size="text-xs" />
                        @else
                            <x-star-rating :rating="null" :count="0" size="text-xs" :label="__('Нов клиент')" />
                        @endif
                        <p class="br-client-sub">
                            {{ __('Член од :date', ['date' => $project->client->created_at->format('M Y')]) }}
                            · {{ __(':count објавени огласи', ['count' => $project->client->projects_count]) }}
                        </p>
                    </div>
                </div>
            @endif
        </section>
    </div>
</div>
