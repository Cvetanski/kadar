<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Верификации на креативци') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="{{ route('admin.users', ['role' => 'pending_verification']) }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:underline mb-6">
                ← {{ __('Назад кон Корисници') }}
            </a>

            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-md p-4 mb-6">{{ session('status') }}</div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 text-red-700 text-sm rounded-md p-4 mb-6">{{ session('error') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Чекаат верификација') }} ({{ $pending->count() }})</h3>

                @if ($pending->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('Нема креативци што чекаат верификација.') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach ($pending as $profile)
                            <div class="flex items-center justify-between border border-gray-200 rounded-md p-4">
                                <div class="flex items-center gap-3">
                                    <x-avatar :user="$profile->user" size="w-10 h-10" textSize="text-sm" />
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $profile->user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $profile->headline }} · {{ $profile->user->email }}</p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ __('Onboarding завршен:') }} {{ $profile->onboarding_completed_at->format('d.m.Y H:i') }}
                                        </p>
                                        @unless ($profile->user->avatar_url)
                                            <p class="text-xs text-red-600 font-medium mt-1">{{ __('⚠ Нема профилна слика') }}</p>
                                        @endunless
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('admin.verifications.verify', $profile) }}">
                                    @csrf
                                    <x-primary-button type="submit">{{ __('Верификувај') }}</x-primary-button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
