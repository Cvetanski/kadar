@php
    $statusLabels = [
        'open' => __('Отворен'),
        'in_progress' => __('Во тек'),
        'completed' => __('Завршен'),
        'cancelled' => __('Откажан'),
    ];
    $proposalStatusLabels = [
        'pending' => __('Чека одговор'),
        'accepted' => __('Прифатена'),
        'rejected' => __('Одбиена'),
        'withdrawn' => __('Повлечена'),
    ];
    $contractStatusLabels = [
        'active' => __('Активен'),
        'delivered' => __('Испорачано'),
        'completed' => __('Завршен'),
        'disputed' => __('Спорен'),
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $project->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-md p-4">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 text-red-700 text-sm rounded-md p-4">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                {{-- Sidebar: status, quick facts, and the contract card all live here so they're
                     visible together without scrolling past the (potentially long) proposals list. --}}
                <div class="lg:col-span-1 lg:order-2 space-y-6 lg:sticky lg:top-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <span @class([
                                'text-xs font-semibold px-2 py-1 rounded-full',
                                'bg-green-100 text-green-700' => $project->status === 'open',
                                'bg-blue-100 text-blue-700' => $project->status === 'in_progress',
                                'bg-gray-100 text-gray-700' => $project->status === 'completed',
                                'bg-red-100 text-red-700' => $project->status === 'cancelled',
                            ])>
                                {{ $statusLabels[$project->status] }}
                            </span>
                        </div>

                        <dl class="mt-4 space-y-3 text-sm">
                            <div>
                                <dt class="text-gray-500">{{ __('Буџет') }}</dt>
                                <dd class="font-medium text-gray-900">
                                    @if ($project->budget_min || $project->budget_max)
                                        {{ $project->budget_min ?? '?' }} – {{ $project->budget_max ?? '?' }} EUR
                                    @else
                                        {{ __('По договор') }}
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">{{ __('Рок') }}</dt>
                                <dd class="font-medium text-gray-900">{{ $project->deadline?->format('d.m.Y') ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">{{ __('Локација') }}</dt>
                                <dd class="font-medium text-gray-900">
                                    {{ $project->remote_ok ? __('Remote') : ($project->city?->name ?? $project->country?->name ?? '—') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">{{ __('Клиент') }}</dt>
                                <dd class="font-medium text-gray-900">
                                    <a href="{{ route('clients.show', $project->client) }}" class="text-indigo-600 hover:underline">{{ $project->client->name }}</a>
                                </dd>
                            </div>
                        </dl>

                        @if ($isOwner && $project->status === 'open')
                            <div class="flex gap-2 mt-5 pt-5 border-t border-gray-200">
                                <a href="{{ route('projects.edit', $project) }}" class="flex-1">
                                    <x-secondary-button type="button" class="w-full justify-center">{{ __('Уреди') }}</x-secondary-button>
                                </a>
                                <x-danger-button type="button" class="flex-1 justify-center" x-data x-on:click="$dispatch('open-modal', 'confirm-project-cancel')">
                                    {{ __('Затвори') }}
                                </x-danger-button>
                            </div>
                        @endif
                    </div>

                    @if ($contract)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-2 border-indigo-100">
                            <div class="flex justify-between items-start">
                                <h3 class="text-lg font-medium text-gray-900">{{ __('Договор') }}</h3>
                                <span @class([
                                    'text-xs font-semibold px-2 py-1 rounded-full',
                                    'bg-blue-100 text-blue-700' => $contract->status === 'active',
                                    'bg-gray-100 text-gray-700' => $contract->status === 'completed',
                                    'bg-red-100 text-red-700' => $contract->status === 'disputed',
                                ])>
                                    {{ $contractStatusLabels[$contract->status] }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2 mt-2">
                                <x-avatar :user="$contract->creatorProfile->user" size="w-6 h-6" textSize="text-xs" />
                                <p class="text-sm text-gray-700">
                                    {{ __('Со') }} <a href="{{ route('creators.show', $contract->creatorProfile) }}" class="text-indigo-600 hover:underline">{{ $contract->creatorProfile->user->name }}</a>
                                </p>
                            </div>
                            <p class="text-sm text-gray-700 mt-1">
                                {{ __('Договорена цена:') }} <span class="font-medium text-gray-900">{{ $contract->agreed_price }} EUR</span>
                            </p>

                            <form method="POST" action="{{ route('messages.start', $contract->creatorProfile) }}" class="mt-4">
                                @csrf
                                <x-secondary-button type="submit" class="w-full justify-center">✉️ {{ __('Прати порака') }}</x-secondary-button>
                            </form>

                            @if ($isOwner && $contract->status === 'active')
                                <form method="POST" action="{{ route('contracts.complete', $contract) }}" class="mt-2">
                                    @csrf
                                    <x-primary-button type="submit" class="w-full justify-center">{{ __('Означи завршено') }}</x-primary-button>
                                </form>
                            @endif

                            @if ($contract->status === 'completed')
                                @if ($myReview)
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <p class="text-sm text-gray-500 mb-1">{{ __('Твоето ревју') }}</p>
                                        <p class="text-sm text-gray-900">{{ str_repeat('★', $myReview->rating) }}{{ str_repeat('☆', 5 - $myReview->rating) }}</p>
                                        @if ($myReview->comment)
                                            <p class="text-sm text-gray-700 mt-1">{{ $myReview->comment }}</p>
                                        @endif
                                    </div>
                                @else
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <h4 class="text-md font-medium text-gray-900 mb-2">{{ __('Остави ревју') }}</h4>
                                        <form method="POST" action="{{ route('reviews.store', $contract) }}">
                                            @csrf
                                            <div>
                                                <x-input-label for="rating" :value="__('Оцена')" />
                                                <select id="rating" name="rating"
                                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                                                    <option value="5">5 ★</option>
                                                    <option value="4">4 ★</option>
                                                    <option value="3">3 ★</option>
                                                    <option value="2">2 ★</option>
                                                    <option value="1">1 ★</option>
                                                </select>
                                                <x-input-error :messages="$errors->get('rating')" class="mt-2" />
                                            </div>
                                            <div class="mt-3">
                                                <x-input-label for="comment" :value="__('Коментар (опционо)')" />
                                                <textarea id="comment" name="comment" rows="3"
                                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"></textarea>
                                                <x-input-error :messages="$errors->get('comment')" class="mt-2" />
                                            </div>
                                            <div class="flex justify-end mt-3">
                                                <x-primary-button type="submit">{{ __('Испрати ревју') }}</x-primary-button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Main content: description + proposals/apply, the part that actually needs scrolling
                     when there are many proposals. --}}
                <div class="lg:col-span-2 lg:order-1 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <p class="text-sm text-gray-500">{{ $project->categories->pluck('name')->join(', ') }}</p>
                        <h3 class="text-lg font-medium text-gray-900 mt-1">{{ $project->title }}</h3>
                        <p class="text-sm text-gray-700 mt-4 whitespace-pre-line">{{ $project->description }}</p>
                    </div>

                    @if ($isOwner)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                {{ __('Понуди') }} ({{ $proposals->count() }})
                                @if ($contract)
                                    <span class="text-sm font-normal text-gray-500">— {{ __('веќе имаш договор со креативец') }}</span>
                                @endif
                            </h3>

                            @if ($proposals->isEmpty())
                                <p class="text-sm text-gray-500">{{ __('Сѐ уште нема пристигнато понуди.') }}</p>
                            @else
                                <div class="space-y-3">
                                    @foreach ($proposals as $proposal)
                                        <div class="border border-gray-200 rounded-md p-4">
                                            <div class="flex justify-between items-start">
                                                <div class="flex gap-3">
                                                    <x-avatar :user="$proposal->creatorProfile->user" size="w-10 h-10" textSize="text-sm" />
                                                    <div>
                                                        <a href="{{ route('creators.show', $proposal->creatorProfile) }}" class="font-medium text-indigo-600 hover:underline">
                                                            {{ $proposal->creatorProfile->user->name }}
                                                        </a>
                                                        <p class="text-sm text-gray-500">{{ $proposal->creatorProfile->headline }}</p>
                                                    </div>
                                                </div>
                                                <span @class([
                                                    'text-xs font-semibold px-2 py-1 rounded-full',
                                                    'bg-yellow-100 text-yellow-700' => $proposal->status === 'pending',
                                                    'bg-green-100 text-green-700' => $proposal->status === 'accepted',
                                                    'bg-red-100 text-red-700' => $proposal->status === 'rejected',
                                                    'bg-gray-100 text-gray-700' => $proposal->status === 'withdrawn',
                                                ])>
                                                    {{ $proposalStatusLabels[$proposal->status] }}
                                                </span>
                                            </div>

                                            <p class="text-sm text-gray-700 mt-3 whitespace-pre-line">{{ $proposal->message }}</p>
                                            <p class="text-sm font-medium text-gray-900 mt-2">{{ __('Цена:') }} {{ $proposal->price }} EUR</p>

                                            <div class="flex gap-2 mt-3">
                                                <form method="POST" action="{{ route('messages.start', $proposal->creatorProfile) }}">
                                                    @csrf
                                                    <x-secondary-button type="submit">✉️ {{ __('Прати порака') }}</x-secondary-button>
                                                </form>

                                                @if ($proposal->status === 'pending' && $project->status === 'open')
                                                    <form method="POST" action="{{ route('proposals.accept', $proposal) }}">
                                                        @csrf
                                                        <x-primary-button type="submit">{{ __('Прифати') }}</x-primary-button>
                                                    </form>
                                                    <form method="POST" action="{{ route('proposals.reject', $proposal) }}">
                                                        @csrf
                                                        <x-danger-button type="submit">{{ __('Одбиј') }}</x-danger-button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @elseif ($existingProposal)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Твојата понуда') }}</h3>
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $existingProposal->message }}</p>
                            <p class="text-sm font-medium text-gray-900 mt-2">{{ __('Цена:') }} {{ $existingProposal->price }} EUR</p>
                            <p class="text-sm text-gray-500 mt-2">{{ __('Статус:') }} {{ $proposalStatusLabels[$existingProposal->status] }}</p>
                        </div>
                    @elseif ($canApply)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Аплицирај на овој проект') }}</h3>
                            <form method="POST" action="{{ route('proposals.store', $project) }}"
                                x-data="{
                                    generating: false,
                                    aiError: '',
                                    async generateCoverLetter() {
                                        this.generating = true;
                                        this.aiError = '';
                                        try {
                                            const res = await fetch('{{ route('proposals.generate-cover-letter', $project) }}', {
                                                method: 'POST',
                                                headers: {
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                    'Accept': 'application/json',
                                                },
                                            });
                                            const data = await res.json();
                                            if (res.ok) {
                                                this.$refs.messageField.value = data.message;
                                            } else {
                                                this.aiError = data.message || @js(__('Настана грешка. Обиди се повторно.'));
                                            }
                                        } catch (e) {
                                            this.aiError = @js(__('Настана грешка. Обиди се повторно.'));
                                        } finally {
                                            this.generating = false;
                                        }
                                    }
                                }">
                                @csrf
                                <div>
                                    <div class="flex items-center justify-between gap-2">
                                        <x-input-label for="message" :value="__('Порака до клиентот')" />
                                        <button type="button" @click="generateCoverLetter" :disabled="generating"
                                            class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap px-3 py-1.5 rounded-full shadow-sm transition">
                                            <span x-show="!generating">✨ {{ __('Состави со AI') }}</span>
                                            <span x-show="generating" x-cloak>✨ {{ __('Составувам...') }}</span>
                                        </button>
                                    </div>
                                    <textarea id="message" name="message" rows="4" x-ref="messageField"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">{{ old('message') }}</textarea>
                                    <p class="text-xs text-red-600 mt-1" x-show="aiError" x-text="aiError" x-cloak></p>
                                    <x-input-error :messages="$errors->get('message')" class="mt-2" />
                                </div>
                                <div class="mt-4">
                                    <x-input-label for="price" :value="__('Твоја цена (EUR)')" />
                                    <x-text-input id="price" name="price" class="block mt-1 w-full" type="number" step="0.01"
                                        min="0" :value="old('price')" />
                                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                </div>
                                <div class="flex justify-end mt-6">
                                    <x-primary-button>{{ __('Испрати понуда') }}</x-primary-button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($isOwner && $project->status === 'open')
        <x-modal name="confirm-project-cancel" focusable>
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Дали сигурно сакаш да го затвориш огласот?') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Проектот') }} „{{ $project->title }}“ {{ __('повеќе нема да прима понуди и ова дејство не може да се врати.') }}
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">
                        {{ __('Откажи') }}
                    </x-secondary-button>

                    <form method="POST" action="{{ route('projects.cancel', $project) }}">
                        @csrf
                        <x-danger-button type="submit">{{ __('Да, затвори оглас') }}</x-danger-button>
                    </form>
                </div>
            </div>
        </x-modal>
    @endif
</x-app-layout>
