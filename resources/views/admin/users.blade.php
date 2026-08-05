<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Корисници') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex gap-2 mb-6 border-b border-gray-200">
                    <a href="{{ route('admin.users', ['role' => 'creator']) }}"
                        class="px-4 py-2 text-sm font-medium border-b-2 {{ $role === 'creator' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ __('Креативци') }} ({{ $creatorCount }})
                    </a>
                    <a href="{{ route('admin.users', ['role' => 'client']) }}"
                        class="px-4 py-2 text-sm font-medium border-b-2 {{ $role === 'client' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ __('Клиенти') }} ({{ $clientCount }})
                    </a>
                </div>

                @if ($users->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('Нема регистрирани корисници во оваа категорија.') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach ($users as $user)
                            <div class="flex items-center justify-between border border-gray-200 rounded-md p-4">
                                <div class="flex items-center gap-3">
                                    <x-avatar :user="$user" size="w-10 h-10" textSize="text-sm" />
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ __('Регистриран:') }} {{ $user->created_at->format('d.m.Y') }}

                                            @if ($role === 'creator')
                                                @if ($user->creatorProfile?->onboarding_completed_at)
                                                    · {{ __('Онбординг завршен') }}
                                                    @if ($user->creatorProfile->verified)
                                                        · <span class="text-green-600 font-medium">{{ __('Верифициран') }}</span>
                                                    @else
                                                        · <span class="text-amber-600 font-medium">{{ __('Чека верификација') }}</span>
                                                    @endif
                                                @else
                                                    · <span class="text-gray-400">{{ __('Онбординг незавршен') }}</span>
                                                @endif
                                            @else
                                                · {{ $user->projects_count }} {{ $user->projects_count === 1 ? __('оглас') : __('огласи') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                @if ($role === 'creator' && $user->creatorProfile)
                                    <a href="{{ route('creators.show', $user->creatorProfile) }}">
                                        <x-secondary-button type="button">{{ __('Профил') }}</x-secondary-button>
                                    </a>
                                @elseif ($role === 'client')
                                    <a href="{{ route('clients.show', $user) }}">
                                        <x-secondary-button type="button">{{ __('Профил') }}</x-secondary-button>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
