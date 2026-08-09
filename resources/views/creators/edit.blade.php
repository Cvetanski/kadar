<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Уреди профил') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @include('partials.creator-fancy-styles')
            <div class="kf-wrap">

                <form method="POST" action="{{ route('creators.update', $creatorProfile) }}" enctype="multipart/form-data" class="kf-form">
                    @csrf
                    @method('PATCH')

                    <div class="kf-card" style="margin-bottom:20px;">
                        <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
                            <div class="kf-avatar-circle {{ Auth::user()->avatar_url ? 'has-image' : '' }}" id="kf-avatar-circle" onclick="document.getElementById('kf-avatar-input').click()">
                                @if (Auth::user()->avatar_url)
                                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ __('Профилна слика на') }} {{ Auth::user()->name }}">
                                @else
                                    <span class="kf-avatar-placeholder">🙂</span>
                                @endif
                                <div class="kf-avatar-cam">📷</div>
                            </div>
                            <input type="file" name="avatar" id="kf-avatar-input" accept="image/*" style="display:none" onchange="kfPreviewAvatar(event)">
                            <div>
                                <p class="kf-card-title" style="margin-bottom:2px;">{{ __('Профилна слика') }}</p>
                                <p class="kf-card-meta" style="margin-bottom:0;">{{ __('JPG или PNG, максимум 9MB') }}</p>
                                <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="kf-card" style="margin-bottom:20px;">
                        <div class="kf-field">
                            <label for="headline">{{ __('Краток опис (headline)') }}</label>
                            <input type="text" id="headline" name="headline" maxlength="100"
                                value="{{ old('headline', $creatorProfile->headline) }}"
                                placeholder="{{ __('Видеограф · Свадби, реклами, настани') }}">
                            <x-input-error :messages="$errors->get('headline')" class="mt-2" />
                        </div>

                        <div class="kf-field">
                            <label for="bio">{{ __('Био') }}</label>
                            <textarea id="bio" name="bio" placeholder="{{ __('Раскажи за твоето искуство и стил на работа...') }}">{{ old('bio', $creatorProfile->bio) }}</textarea>
                            <x-input-error :messages="$errors->get('bio')" class="mt-2" />
                        </div>

                        <div class="kf-two-col">
                            <div class="kf-field">
                                <label for="hourly_rate">{{ __('Цена по час (€)') }}</label>
                                <input type="number" id="hourly_rate" name="hourly_rate" min="0" step="0.01"
                                    value="{{ old('hourly_rate', $creatorProfile->hourly_rate) }}" placeholder="35">
                                <x-input-error :messages="$errors->get('hourly_rate')" class="mt-2" />
                            </div>
                            <div class="kf-field">
                                <label for="experience_years">{{ __('Години искуство') }}</label>
                                <input type="number" id="experience_years" name="experience_years" min="0"
                                    value="{{ old('experience_years', $creatorProfile->experience_years) }}" placeholder="6">
                                <x-input-error :messages="$errors->get('experience_years')" class="mt-2" />
                            </div>
                        </div>

                        <label class="kf-checkbox-row" style="margin-top:6px;">
                            <input type="checkbox" name="remote_ok" value="1" {{ old('remote_ok', $creatorProfile->remote_ok) ? 'checked' : '' }}>
                            {{ __('Достапен сум и за работа од далечина') }}
                        </label>
                    </div>

                    <div class="kf-card" style="margin-bottom:20px;">
                        <p class="kf-card-title">{{ __('Категории') }}</p>
                        <div class="kf-tile-grid" style="margin-top:12px;">
                            @foreach ($categories as $category)
                                <label class="kf-tile-label">
                                    <input type="checkbox" class="kf-tile-checkbox" name="category_ids[]" value="{{ $category->id }}"
                                        {{ in_array($category->id, old('category_ids', $selectedCategoryIds)) ? 'checked' : '' }}>
                                    <div class="kf-tile">
                                        <div class="kf-tile-icon">{{ $category->icon }}</div>
                                        <div class="kf-tile-name">{{ $category->name }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="kf-card" style="margin-bottom:20px;">
                        <p class="kf-card-title">{{ __('Вештини') }}</p>
                        @foreach ($skillsByCategory as $categoryId => $skills)
                            <div style="margin-top:16px;">
                                <div class="kf-card-meta" style="text-transform:uppercase;font-weight:700;letter-spacing:0.03em;">
                                    {{ $categories->firstWhere('id', $categoryId)?->icon }} {{ $categories->firstWhere('id', $categoryId)?->name }}
                                </div>
                                <div class="kf-pill-row" style="margin-top:8px;">
                                    @foreach ($skills as $skill)
                                        <label class="kf-pill-label">
                                            <input type="checkbox" class="kf-pill-checkbox" name="skill_ids[]" value="{{ $skill->id }}"
                                                {{ in_array($skill->id, old('skill_ids', $selectedSkillIds)) ? 'checked' : '' }}>
                                            <span class="kf-pill-btn">{{ $skill->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <livewire:portfolio-manager :creator-profile="$creatorProfile" />

                    <div class="kf-card" style="margin-bottom:20px;">
                        <p class="kf-card-title" style="margin-bottom:16px;">{{ __('Социјални мрежи') }}</p>

                        <div class="kf-social-row">
                            <div class="kf-social-icon kf-instagram"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></div>
                            <input type="url" name="instagram_url" placeholder="https://instagram.com/..."
                                value="{{ old('instagram_url', $creatorProfile->instagram_url) }}">
                        </div>
                        <x-input-error :messages="$errors->get('instagram_url')" class="mb-3" />

                        <div class="kf-social-row">
                            <div class="kf-social-icon kf-facebook"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg></div>
                            <input type="url" name="facebook_url" placeholder="https://facebook.com/..."
                                value="{{ old('facebook_url', $creatorProfile->facebook_url) }}">
                        </div>
                        <x-input-error :messages="$errors->get('facebook_url')" class="mb-3" />

                        <div class="kf-social-row" style="margin-bottom:0;">
                            <div class="kf-social-icon kf-website">🌐</div>
                            <input type="url" name="website_url" placeholder="https://..."
                                value="{{ old('website_url', $creatorProfile->website_url) }}">
                        </div>
                        <x-input-error :messages="$errors->get('website_url')" class="mt-2" />
                    </div>

                    <div class="kf-form-actions">
                        <button type="submit" class="kf-btn">{{ __('Зачувај промени') }}</button>
                        <a href="{{ route('creators.show', $creatorProfile) }}" class="kf-clear" style="align-self:center;">{{ __('Откажи') }}</a>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function kfPreviewAvatar(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                const circle = document.getElementById('kf-avatar-circle');
                circle.classList.add('has-image');
                let img = circle.querySelector('img');
                let placeholder = circle.querySelector('.kf-avatar-placeholder');
                if (placeholder) placeholder.style.display = 'none';
                if (!img) {
                    img = document.createElement('img');
                    img.alt = {{ Illuminate\Support\Js::from(__('Преглед на профилна слика')) }};
                    circle.insertBefore(img, circle.firstChild);
                }
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    </script>
</x-app-layout>
