@php
    $citiesByCountry = $countries->mapWithKeys(fn ($country) => [
        $country->id => $country->cities->map(fn ($city) => ['id' => $city->id, 'name' => $city->name])->values(),
    ]);
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Креирај Оглас') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('projects.store') }}"
                    x-data="{
                        remoteOk: {{ old('remote_ok') ? 'true' : 'false' }},
                        budgetNegotiable: {{ old('budget_negotiable') ? 'true' : 'false' }},
                        countryId: {{ \Illuminate\Support\Js::from(old('country_id') !== null && old('country_id') !== '' ? (int) old('country_id') : null) }},
                        cityId: {{ \Illuminate\Support\Js::from(old('city_id') !== null && old('city_id') !== '' ? (int) old('city_id') : null) }},
                        citiesByCountry: {{ \Illuminate\Support\Js::from($citiesByCountry) }},
                        get cities() { return this.countryId ? (this.citiesByCountry[this.countryId] || []) : []; },
                        categoryIds: {{ \Illuminate\Support\Js::from(array_map('strval', old('category_ids', []))) }},
                        improving: false,
                        improveError: '',
                        async improveWithAi() {
                            const title = this.$refs.titleField.value.trim();
                            const description = this.$refs.descriptionField.value.trim();
                            if (! title || ! description) {
                                this.improveError = @js(__('Внеси наслов и опис пред да го користиш AI подобрувањето.'));
                                return;
                            }
                            this.improving = true;
                            this.improveError = '';
                            try {
                                const res = await fetch('{{ route('projects.improve-description') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({ title, description, category_ids: this.categoryIds }),
                                });
                                const data = await res.json();
                                if (res.ok) {
                                    this.$refs.titleField.value = data.title;
                                    this.$refs.descriptionField.value = data.description;
                                } else {
                                    this.improveError = data.message || @js(__('Настана грешка. Обиди се повторно.'));
                                }
                            } catch (e) {
                                this.improveError = @js(__('Настана грешка. Обиди се повторно.'));
                            } finally {
                                this.improving = false;
                            }
                        }
                    }">
                    @csrf

                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-sm font-semibold text-gray-700">{{ __('Наслов и опис') }}</h3>
                        <button type="button" @click="improveWithAi" :disabled="improving"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap px-3 py-1.5 rounded-full shadow-sm transition">
                            <span x-show="!improving">✨ {{ __('Подобри со AI') }}</span>
                            <span x-show="improving" x-cloak>✨ {{ __('Подобрувам...') }}</span>
                        </button>
                    </div>
                    <p class="text-xs text-red-600 mt-1" x-show="improveError" x-text="improveError" x-cloak></p>

                    <div class="mt-2">
                        <x-input-label for="title" :value="__('Наслов')" />
                        <x-text-input id="title" name="title" class="block mt-1 w-full" type="text" :value="old('title')" x-ref="titleField" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div class="mt-3">
                        <x-input-label for="description" :value="__('Опис')" />
                        <textarea id="description" name="description" rows="4" x-ref="descriptionField"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="mt-3">
                        <x-input-label :value="__('Категории (можеш да избереш повеќе)')" />
                        <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach ($categories as $category)
                                <label class="flex items-center gap-2 border border-gray-200 rounded-md px-3 py-2 text-sm cursor-pointer hover:bg-gray-50">
                                    <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" x-model="categoryIds"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                                    {{ $category->icon }} {{ $category->name }}
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('category_ids')" class="mt-2" />
                    </div>

                    @if ($skillsByCategory->isNotEmpty())
                        <div class="mt-3" x-show="categoryIds.length > 0" x-cloak>
                            <x-input-label :value="__('Потребни вештини (опционално)')" />
                            <div class="mt-2 space-y-3">
                                @foreach ($skillsByCategory as $categoryId => $skills)
                                    <div x-show="categoryIds.includes('{{ $categoryId }}')" x-cloak>
                                        <p class="text-xs font-semibold text-gray-500 mb-1.5">{{ $categories->firstWhere('id', $categoryId)?->name }}</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($skills as $skill)
                                                <label class="flex items-center gap-1.5 border border-gray-200 rounded-full px-3 py-1 text-xs cursor-pointer hover:bg-gray-50">
                                                    <input type="checkbox" name="skill_ids[]" value="{{ $skill->id }}"
                                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                        {{ in_array($skill->id, old('skill_ids', [])) ? 'checked' : '' }}>
                                                    {{ $skill->name }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('skill_ids')" class="mt-2" />
                        </div>
                    @endif

                    <div class="mt-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="budget_negotiable" value="1" x-model="budgetNegotiable"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ms-2 text-sm text-gray-700">{{ __('Цената е по договор / да се дискутира') }}</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-3">
                        <div x-show="! budgetNegotiable">
                            <x-input-label for="budget_min" :value="__('Буџет од (EUR)')" />
                            <x-text-input id="budget_min" name="budget_min" class="block mt-1 w-full" type="number"
                                step="0.01" min="0" :value="old('budget_min')" :disabled="old('budget_negotiable') ? true : false" x-bind:disabled="budgetNegotiable" />
                            <x-input-error :messages="$errors->get('budget_min')" class="mt-2" />
                        </div>
                        <div x-show="! budgetNegotiable">
                            <x-input-label for="budget_max" :value="__('Буџет до (EUR)')" />
                            <x-text-input id="budget_max" name="budget_max" class="block mt-1 w-full" type="number"
                                step="0.01" min="0" :value="old('budget_max')" :disabled="old('budget_negotiable') ? true : false" x-bind:disabled="budgetNegotiable" />
                            <x-input-error :messages="$errors->get('budget_max')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="deadline" :value="__('Рок')" />
                            <x-text-input id="deadline" name="deadline" class="block mt-1 w-full" type="date" :value="old('deadline')" />
                            <x-input-error :messages="$errors->get('deadline')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="remote_ok" value="1" x-model="remoteOk"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ms-2 text-sm text-gray-700">{{ __('Проектот е remote (не бара физичко присуство)') }}</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3" x-show="! remoteOk">
                        <div>
                            <x-input-label for="country_id" :value="__('Земја')" />
                            <select id="country_id" name="country_id" x-model.number="countryId" @change="cityId = null"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                                <option value="">{{ __('Избери земја') }}</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('country_id')" class="mt-2" />
                        </div>

                        <div x-show="cities.length > 0">
                            <x-input-label for="city_id" :value="__('Град (опционо)')" />
                            <select id="city_id" name="city_id" x-model.number="cityId"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                                <option value="">{{ __('Избери град') }}</option>
                                <template x-for="city in cities" :key="city.id">
                                    <option :value="city.id" x-text="city.name"></option>
                                </template>
                            </select>
                            <x-input-error :messages="$errors->get('city_id')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <x-primary-button>{{ __('Објави проект') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
