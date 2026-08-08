<?php

use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Skill;
use App\Services\AvatarUploadService;
use App\Services\GeoIpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public int $step = 1;

    public bool $showSkipConfirm = false;

    // Step 4 — avatar
    public $avatarUpload = null;

    // Step 1 — category
    public array $categoryIds = [];

    // Step 2 — skills
    public array $skillIds = [];

    // Step 3 — location
    public ?int $countryId = null;

    public ?int $cityId = null;

    public bool $remoteOk = false;

    // Step 4 — profile
    public string $headline = '';

    public string $bio = '';

    public ?float $hourlyRate = null;

    public ?int $experienceYears = null;

    // Step 5 — portfolio (kept only in component state until final submit)
    public array $portfolioItems = [];

    public string $newPortfolioTitle = '';

    public string $newPortfolioType = 'video';

    public string $newPortfolioUrl = '';

    // Step 6 — social
    public ?string $instagramUrl = null;

    public ?string $facebookUrl = null;

    public ?string $websiteUrl = null;

    /**
     * Best-effort country prefill from the visitor's IP — purely a UX
     * convenience, so a failed/slow lookup must never block mounting.
     */
    public function mount(GeoIpService $geoIpService): void
    {
        if ($this->countryId) {
            return;
        }

        $countryCode = $geoIpService->guessCountryCode(request()->ip() ?? '');

        if ($countryCode) {
            $this->countryId = Country::where('code', $countryCode)->value('id');
        }
    }

    private function categoryDescriptions(): array
    {
        return [
            'video-production' => __('Свадби, реклами, настани, спотови'),
            'photography' => __('Продукт, портрет, настан'),
            'video-editing' => __('Монтажа, колор грејдинг'),
            'digital-marketing' => __('Content, раст, рекламирање'),
            'design' => __('Графички дизајн, брендинг, лого'),
        ];
    }

    private function stepNames(): array
    {
        return [
            1 => __('Категорија'),
            2 => __('Вештини'),
            3 => __('Локација'),
            4 => __('Профил'),
            5 => __('Портфолио'),
            6 => __('Социјални мрежи'),
            7 => __('Преглед'),
        ];
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('id')->get();
    }

    #[Computed]
    public function skillsByCategory()
    {
        if (empty($this->categoryIds)) {
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

        return City::where('country_id', $this->countryId)->orderBy('id')->get();
    }

    public function categoryDescription(string $slug): string
    {
        return $this->categoryDescriptions()[$slug] ?? '';
    }

    public function stepName(): string
    {
        return $this->stepNames()[$this->step] ?? '';
    }

    public function toggleCategory(int $categoryId): void
    {
        if (in_array($categoryId, $this->categoryIds, true)) {
            $this->categoryIds = array_values(array_diff($this->categoryIds, [$categoryId]));
        } else {
            $this->categoryIds[] = $categoryId;
        }

        // Drop any selected skills that belonged only to a category that just got deselected.
        $this->skillIds = Skill::whereIn('id', $this->skillIds)
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $this->categoryIds))
            ->pluck('id')
            ->all();
    }

    public function toggleSkill(int $skillId): void
    {
        if (in_array($skillId, $this->skillIds, true)) {
            $this->skillIds = array_values(array_diff($this->skillIds, [$skillId]));
        } else {
            $this->skillIds[] = $skillId;
        }
    }

    public function updatedCountryId(): void
    {
        $this->cityId = null;
    }

    public function updatedAvatarUpload(): void
    {
        $this->validateOnly('avatarUpload', [
            'avatarUpload' => ['image', 'max:2048'],
        ]);
    }

    public function removeAvatar(): void
    {
        $this->avatarUpload = null;
    }

    public function addPortfolioItem(): void
    {
        $this->validate([
            'newPortfolioUrl' => ['required', 'url', 'max:2048'],
            'newPortfolioType' => ['required', 'in:video,image'],
        ], $this->validationMessages());

        $this->portfolioItems[] = [
            'title' => $this->newPortfolioTitle !== '' ? $this->newPortfolioTitle : __('Без наслов'),
            'media_type' => $this->newPortfolioType,
            'media_url' => $this->newPortfolioUrl,
        ];

        $this->newPortfolioTitle = '';
        $this->newPortfolioUrl = '';
        $this->resetErrorBag(['newPortfolioUrl', 'newPortfolioType']);
    }

    public function removePortfolioItem(int $index): void
    {
        unset($this->portfolioItems[$index]);
        $this->portfolioItems = array_values($this->portfolioItems);
    }

    protected function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'categoryIds' => ['required', 'array', 'min:1'],
                'categoryIds.*' => ['integer', 'exists:categories,id'],
            ],
            2 => [
                'skillIds' => ['array', 'min:3'],
                'skillIds.*' => ['integer', 'exists:skills,id'],
            ],
            3 => [
                'countryId' => ['required', 'exists:countries,id'],
                'cityId' => ['nullable', 'exists:cities,id'],
            ],
            4 => [
                'avatarUpload' => ['nullable', 'image', 'max:2048'],
                'headline' => ['required', 'string', 'max:100'],
                'bio' => ['nullable', 'string', 'max:2000'],
                'hourlyRate' => ['nullable', 'numeric', 'min:0', 'max:9999'],
                'experienceYears' => ['nullable', 'integer', 'min:0', 'max:60'],
            ],
            5 => [
                'portfolioItems' => ['array', 'min:1'],
            ],
            6 => [
                'instagramUrl' => ['nullable', 'url', 'max:255'],
                'facebookUrl' => ['nullable', 'url', 'max:255'],
                'websiteUrl' => ['nullable', 'url', 'max:255'],
            ],
            default => [],
        };
    }

    public function nextStep(): void
    {
        $this->validate($this->rulesForStep($this->step), $this->validationMessages());

        if ($this->step < 7) {
            $this->step++;
        }
    }

    private function validationMessages(): array
    {
        return [
            'countryId.required' => __('Избери земја.'),
            'countryId.exists' => __('Избери валидна земја.'),
            'cityId.exists' => __('Избери валиден град.'),
            'headline.required' => __('Внеси краток опис.'),
            'headline.max' => __('Краткиот опис може да има највеќе 100 карактери.'),
            'skillIds.min' => __('Избери барем 3 вештини вкупно.'),
            'portfolioItems.min' => __('Додади барем 1 линк кон твоја работа.'),
            'newPortfolioUrl.required' => __('Внеси линк.'),
            'newPortfolioUrl.url' => __('Ова не е валиден линк.'),
            'newPortfolioType.required' => __('Избери тип на содржина.'),
            'instagramUrl.url' => __('Ова не е валиден линк.'),
            'facebookUrl.url' => __('Ова не е валиден линк.'),
            'websiteUrl.url' => __('Ова не е валиден линк.'),
        ];
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function skip()
    {
        $user = Auth::user();

        $user->creatorProfile()->firstOrCreate(['user_id' => $user->id])
            ->update(['onboarding_skipped_at' => now()]);

        session()->flash('status', __('Може да го завршиш профилот кога сакаш — потсетниците ќе ти помогнат да не заборавиш.'));

        return redirect()->route('dashboard');
    }

    public function submit()
    {
        $rules = array_merge(
            $this->rulesForStep(1),
            $this->rulesForStep(2),
            $this->rulesForStep(3),
            $this->rulesForStep(4),
            $this->rulesForStep(5),
            $this->rulesForStep(6),
        );

        $this->validate($rules, $this->validationMessages());

        $user = Auth::user();

        DB::transaction(function () use ($user) {
            $profile = $user->creatorProfile()->firstOrCreate(['user_id' => $user->id]);

            $profile->update([
                'headline' => $this->headline,
                'bio' => $this->bio !== '' ? $this->bio : null,
                'hourly_rate' => $this->hourlyRate,
                'experience_years' => $this->experienceYears ?? 0,
                'remote_ok' => $this->remoteOk,
                'instagram_url' => $this->instagramUrl,
                'facebook_url' => $this->facebookUrl,
                'website_url' => $this->websiteUrl,
                'onboarding_completed_at' => now(),
            ]);

            $userUpdate = [
                'country_id' => $this->countryId,
                'city_id' => $this->remoteOk ? null : $this->cityId,
            ];

            if ($this->avatarUpload) {
                $userUpdate['avatar_url'] = app(AvatarUploadService::class)->store($this->avatarUpload);
            }

            $user->update($userUpdate);

            $profile->categories()->sync($this->categoryIds);
            $profile->skills()->sync($this->skillIds);

            foreach ($this->portfolioItems as $index => $item) {
                $profile->portfolioItems()->create([
                    'title' => $item['title'],
                    'media_type' => $item['media_type'],
                    'media_url' => $item['media_url'],
                    'sort_order' => $index,
                ]);
            }
        });

        session()->flash('status', __('Профилот е испратен за верификација.'));

        return redirect()->route('dashboard');
    }
};
?>

<div>
    <div class="progress-head">
        <div class="progress-row">
            <div class="progress-label">{{ __('Чекор') }} {{ $step }} {{ __('од') }} 7</div>
            <div class="progress-step-name">{{ $this->stepName() }}</div>
        </div>
        <div class="progress-track"><div class="progress-fill" style="width: {{ $step / 7 * 100 }}%"></div></div>
        <div style="text-align:right;margin-top:8px;">
            <button type="button" wire:click="$set('showSkipConfirm', true)"
                style="background:transparent;color:#D6249F;font-size:11.5px;font-weight:800;letter-spacing:0.04em;
                    padding:5px 14px;border-radius:999px;border:1px solid #D6249F;cursor:pointer;
                    box-shadow:0 0 8px rgba(214,36,159,.5), 0 0 2px rgba(214,36,159,.8);
                    transition:box-shadow .15s ease, transform .15s ease;"
                onmouseover="this.style.boxShadow='0 0 14px rgba(214,36,159,.75), 0 0 4px rgba(214,36,159,.9)'"
                onmouseout="this.style.boxShadow='0 0 8px rgba(214,36,159,.5), 0 0 2px rgba(214,36,159,.8)'">
                {{ __('Прескокни за сега') }} →
            </button>
        </div>

        @if ($showSkipConfirm)
            <div style="position:fixed;inset:0;background:rgba(20,23,31,.5);z-index:100;display:flex;
                align-items:center;justify-content:center;padding:16px;" wire:click.self="$set('showSkipConfirm', false)">
                <div class="card" style="max-width:420px;width:100%;padding:28px;">
                    <h3 style="font-size:18px;font-weight:800;margin-bottom:8px;">{{ __('Прескокни за сега?') }}</h3>
                    <p style="font-size:14px;color:var(--text-dim);line-height:1.55;margin-bottom:22px;">
                        {{ __('Ќе можеш да го завршиш профилот подоцна од твојата контролна табла. Клиентите нема да те гледаат додека профилот не е завршен и верифициран.') }}
                    </p>
                    <div style="display:flex;justify-content:flex-end;gap:10px;">
                        <button type="button" class="btn btn-back" wire:click="$set('showSkipConfirm', false)">{{ __('Откажи') }}</button>
                        <button type="button" class="btn btn-next" wire:click="skip">{{ __('Прескокни за сега') }}</button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="card">

        @if ($step === 1)
            <div class="step-title">{{ __('Која е твојата специјалност?') }}</div>
            <div class="step-sub">{{ __('Избери една или повеќе категории — профилот ти ќе се појавува во сите.') }}</div>
            <div class="tile-grid">
                @foreach ($this->categories as $category)
                    <div wire:key="category-{{ $category->id }}"
                        class="tile {{ in_array($category->id, $categoryIds) ? 'selected' : '' }}"
                        wire:click="toggleCategory({{ $category->id }})">
                        <div class="tile-check">✓</div>
                        <div class="icon">{{ $category->icon }}</div>
                        <div class="name">{{ $category->name }}</div>
                        <div class="desc">{{ $this->categoryDescription($category->slug) }}</div>
                    </div>
                @endforeach
            </div>
            @error('categoryIds') <div class="field-error">{{ $message }}</div> @enderror
        @endif

        @if ($step === 2)
            <div class="step-title">{{ __('Кои се твоите вештини?') }}</div>
            <div class="step-sub">
                {{ __('Прикажани се тагови поврзани со твоите избрани категории.') }}
                {{ __('Избери барем 3 вкупно.') }}
                <strong style="color:{{ count($skillIds) >= 3 ? 'var(--green)' : 'var(--blue)' }};">({{ count($skillIds) }}/3)</strong>
            </div>
            @if ($this->skillsByCategory->isEmpty())
                <div class="empty-note">{{ __('Прво избери барем една категорија во чекор 1') }}</div>
            @else
                @foreach ($this->skillsByCategory as $categoryId => $skills)
                    <div class="skill-group" wire:key="skill-group-{{ $categoryId }}">
                        <div class="skill-group-title">{{ $this->categories->firstWhere('id', $categoryId)?->icon }} {{ $this->categories->firstWhere('id', $categoryId)?->name }}</div>
                        <div class="pill-row">
                            @foreach ($skills as $skill)
                                <div wire:key="skill-{{ $skill->id }}"
                                    class="pill {{ in_array($skill->id, $skillIds) ? 'selected' : '' }}"
                                    wire:click="toggleSkill({{ $skill->id }})">
                                    {{ $skill->name }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                @error('skillIds') <div class="field-error">{{ $message }}</div> @enderror
            @endif
        @endif

        @if ($step === 3)
            <div class="step-title">{{ __('Каде се наоѓаш?') }}</div>
            <div class="step-sub">{{ __('Клиентите можат да филтрираат по локација.') }}</div>
            <label for="country">{{ __('Земја') }}</label>
            <select id="country" wire:model.live="countryId">
                <option value="">{{ __('Избери земја') }}</option>
                @foreach ($this->countries as $country)
                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                @endforeach
            </select>
            @error('countryId') <div class="field-error">{{ $message }}</div> @enderror

            <label for="city">{{ __('Град') }}</label>
            <select id="city" wire:model="cityId">
                <option value="">{{ __('Избери град') }}</option>
                @foreach ($this->cities as $city)
                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                @endforeach
            </select>
            @error('cityId') <div class="field-error">{{ $message }}</div> @enderror

            <div class="checkbox-row">
                <input type="checkbox" id="remote" wire:model="remoteOk">
                <label for="remote" style="margin:0;font-weight:400;">{{ __('Достапен сум и за работа од далечина') }}</label>
            </div>
        @endif

        @if ($step === 4)
            <div class="step-title">{{ __('Кажи ни повеќе за себе') }}</div>
            <div class="step-sub">{{ __('Ова ќе биде прикажано на јавниот профил.') }}</div>

            <div style="display:flex;justify-content:center;">
                <div class="avatar-circle {{ $avatarUpload ? 'has-image' : '' }}" onclick="document.getElementById('avatar-input').click()">
                    @if ($avatarUpload)
                        <img src="{{ $avatarUpload->temporaryUrl() }}" alt="{{ __('Преглед на профилна слика') }}">
                    @else
                        <span class="placeholder-icon">🙂</span>
                    @endif
                    <div class="avatar-cam">📷</div>
                </div>
            </div>
            <input type="file" id="avatar-input" wire:model="avatarUpload" accept="image/*" style="display:none">
            <div wire:loading wire:target="avatarUpload" style="text-align:center;font-size:12.5px;color:var(--text-dim);margin-top:8px;">{{ __('Се вчитува...') }}</div>
            @error('avatarUpload') <div class="field-error" style="text-align:center;">{{ $message }}</div> @enderror
            @if ($avatarUpload)
                <div style="text-align:center;margin-top:8px;">
                    <button type="button" class="remove-btn" style="font-size:13px;" wire:click="removeAvatar">{{ __('Отстрани слика') }}</button>
                </div>
            @endif

            <label for="headline">{{ __('Краток опис (headline)') }}</label>
            <input type="text" id="headline" wire:model="headline" maxlength="100" placeholder="{{ __('Видеограф · Свадби, реклами, настани') }}">
            @error('headline') <div class="field-error">{{ $message }}</div> @enderror

            <label for="bio">{{ __('Био') }}</label>
            <textarea id="bio" wire:model="bio" placeholder="{{ __('Раскажи за твоето искуство и стил на работа...') }}"></textarea>
            @error('bio') <div class="field-error">{{ $message }}</div> @enderror

            <div class="two-col">
                <div>
                    <label for="rate">{{ __('Цена по час (€)') }} <strong style="color:var(--text-dim);">({{ __('опционално') }})</strong></label>
                    <input type="number" id="rate" wire:model="hourlyRate" min="0" step="0.01" placeholder="35">
                    @error('hourlyRate') <div class="field-error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="experience">{{ __('Години искуство') }}</label>
                    <input type="number" id="experience" wire:model="experienceYears" min="0" placeholder="6">
                    @error('experienceYears') <div class="field-error">{{ $message }}</div> @enderror
                </div>
            </div>
        @endif

        @if ($step === 5)
            <div class="step-title">{{ __('Прикажи ја твојата работа') }}</div>
            <div class="step-sub">{{ __('Долепи линк до постоечка содржина (YouTube, Vimeo, Instagram...). Барем 1 задолжителна.') }}</div>
            <div class="add-row">
                <div><label>{{ __('Наслов') }}</label><input type="text" wire:model="newPortfolioTitle" placeholder="{{ __('Свадба - Марија и Стефан') }}"></div>
                <div>
                    <label>{{ __('Тип') }}</label>
                    <select wire:model="newPortfolioType">
                        <option value="video">{{ __('Видео') }}</option>
                        <option value="image">{{ __('Слика') }}</option>
                    </select>
                </div>
                <div><label>{{ __('Линк') }}</label><input type="url" wire:model="newPortfolioUrl" placeholder="https://..."></div>
                <button type="button" class="btn-add" wire:click="addPortfolioItem">{{ __('Додади') }}</button>
            </div>
            @error('newPortfolioUrl') <div class="field-error">{{ $message }}</div> @enderror

            <div class="portfolio-list">
                @foreach ($portfolioItems as $index => $item)
                    <div class="portfolio-item" wire:key="portfolio-{{ $index }}">
                        <div><strong>{{ $item['title'] }}</strong> <span class="meta">· {{ $item['media_type'] === 'video' ? __('Видео') : __('Слика') }}</span></div>
                        <button type="button" class="remove-btn" wire:click="removePortfolioItem({{ $index }})">✕</button>
                    </div>
                @endforeach
            </div>
            @if (empty($portfolioItems))
                <div class="empty-note">{{ __('Сеуште нема додадено ставки') }}</div>
            @endif
            @error('portfolioItems') <div class="field-error">{{ $message }}</div> @enderror
        @endif

        @if ($step === 6)
            <div class="step-title">{{ __('Социјални мрежи') }}</div>
            <div class="step-sub">{{ __('Опционално, но помага на клиентите да ти веруваат повеќе.') }}</div>
            <label for="instagram">{{ __('Instagram') }}</label>
            <input type="url" id="instagram" wire:model="instagramUrl" placeholder="https://instagram.com/...">
            @error('instagramUrl') <div class="field-error">{{ $message }}</div> @enderror

            <label for="facebook">{{ __('Facebook') }}</label>
            <input type="url" id="facebook" wire:model="facebookUrl" placeholder="https://facebook.com/...">
            @error('facebookUrl') <div class="field-error">{{ $message }}</div> @enderror

            <label for="website">{{ __('Веб-сајт') }}</label>
            <input type="url" id="website" wire:model="websiteUrl" placeholder="https://...">
            @error('websiteUrl') <div class="field-error">{{ $message }}</div> @enderror
        @endif

        @if ($step === 7)
            <div class="step-title">{{ __('Прегледај пред да објавиш') }}</div>
            <div class="step-sub">{{ __('Провери дали сè е точно пред да го испратиш за верификација.') }}</div>

            <div class="review-block">
                <div class="review-label">{{ __('Категории') }}</div>
                <div class="review-tags">
                    @forelse ($this->categories->whereIn('id', $categoryIds) as $category)
                        <span class="review-tag">{{ $category->name }}</span>
                    @empty
                        <span class="review-value">—</span>
                    @endforelse
                </div>
            </div>

            <div class="review-block">
                <div class="review-label">{{ __('Вештини') }}</div>
                <div class="review-tags">
                    @forelse (Skill::whereIn('id', $skillIds)->get() as $skill)
                        <span class="review-tag">{{ $skill->name }}</span>
                    @empty
                        <span class="review-value">—</span>
                    @endforelse
                </div>
            </div>

            <div class="review-block">
                <div class="review-label">{{ __('Локација') }}</div>
                <div class="review-value">
                    {{ optional($this->cities->firstWhere('id', $cityId))->name ?? '—' }},
                    {{ optional($this->countries->firstWhere('id', $countryId))->name ?? '—' }}
                    @if ($remoteOk) · {{ __('Достапен за далечина') }} @endif
                </div>
            </div>

            <div class="review-block">
                <div class="review-label">{{ __('Профил') }}</div>
                <div class="review-value">
                    <strong>{{ $headline ?: '—' }}</strong><br>
                    {{ $bio ?: '—' }}<br>
                    {{ $hourlyRate ?? '—' }}€/{{ __('ч') }} · {{ $experienceYears ?? '—' }} {{ __('години искуство') }}
                </div>
            </div>

            <div class="review-block">
                <div class="review-label">{{ __('Портфолио') }}</div>
                <div class="review-value">{{ count($portfolioItems) }} {{ __('ставки додадени') }}</div>
            </div>

            <div class="verify-note">🕒 {{ __('По испраќање, профилот чека рачна верификација (обично до 24 часа) пред да стане јавно видлив.') }}</div>
        @endif

        <div class="nav-row">
            <button type="button" class="btn btn-back" wire:click="previousStep" @if ($step === 1) disabled @endif>← {{ __('Назад') }}</button>
            @if ($step < 7)
                <button type="button" class="btn btn-next" wire:click="nextStep">{{ __('Следно →') }}</button>
            @else
                <button type="button" class="btn btn-next" wire:click="submit">{{ __('Испрати за верификација →') }}</button>
            @endif
        </div>
    </div>
</div>
