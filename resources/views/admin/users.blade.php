<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Корисници') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-md p-4 mb-6">{{ session('status') }}</div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 text-red-700 text-sm rounded-md p-4 mb-6">{{ session('error') }}</div>
            @endif

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
                    <a href="{{ route('admin.users', ['role' => 'pending_verification']) }}"
                        class="px-4 py-2 text-sm font-medium border-b-2 {{ $role === 'pending_verification' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ __('Корисници за верификација') }} ({{ $pendingVerificationCount }})
                    </a>
                    <a href="{{ route('admin.users', ['role' => 'verified']) }}"
                        class="px-4 py-2 text-sm font-medium border-b-2 {{ $role === 'verified' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ __('Верифицирани корисници') }} ({{ $verifiedCount }})
                    </a>
                </div>

                @if ($role === 'creator')
                    <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-md p-4 mb-6">
                        <p class="text-sm text-gray-600">
                            {{ __(':count креативци немаат завршено онбординг.', ['count' => $incompleteOnboardingCount]) }}
                        </p>

                        <x-primary-button type="button" x-data x-on:click="$dispatch('open-modal', 'confirm-remind-onboarding')">
                            {{ __('Испрати потсетник') }}
                        </x-primary-button>
                    </div>

                    <x-modal name="confirm-remind-onboarding" focusable>
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Испрати потсетник до :count креативци?', ['count' => $incompleteOnboardingCount]) }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Секој креативец што сѐ уште нема завршено онбординг ќе добие email со потсетник да го доврши профилот.') }}
                            </p>

                            <div class="mt-6 flex justify-end gap-3">
                                <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                    {{ __('Откажи') }}
                                </x-secondary-button>

                                <form method="POST" action="{{ route('admin.creators.remind-onboarding') }}">
                                    @csrf
                                    <x-primary-button type="submit">{{ __('Да, испрати') }}</x-primary-button>
                                </form>
                            </div>
                        </div>
                    </x-modal>
                @endif

                @if ($role === 'pending_verification')
                    @if ($pending->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('Нема креативци што чекаат верификација.') }}</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($pending as $profile)
                                <div class="flex items-center justify-between border border-gray-200 rounded-md p-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('creators.show', $profile) }}">
                                            <x-avatar :user="$profile->user" size="w-10 h-10" textSize="text-sm" />
                                        </a>
                                        <div>
                                            <a href="{{ route('creators.show', $profile) }}" class="font-medium text-gray-900 hover:underline">{{ $profile->user->name }}</a>
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
                @elseif ($users->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('Нема регистрирани корисници во оваа категорија.') }}</p>
                @else
                    @php $isCreatorLike = in_array($role, ['creator', 'verified'], true); @endphp
                    <div class="space-y-3">
                        @foreach ($users as $user)
                            <div class="flex items-center justify-between border border-gray-200 rounded-md p-4">
                                <div class="flex items-center gap-3">
                                    @if ($isCreatorLike && $user->creatorProfile)
                                        <a href="{{ route('creators.show', $user->creatorProfile) }}">
                                            <x-avatar :user="$user" size="w-10 h-10" textSize="text-sm" />
                                        </a>
                                    @else
                                        <x-avatar :user="$user" size="w-10 h-10" textSize="text-sm" />
                                    @endif
                                    <div>
                                        @if ($isCreatorLike && $user->creatorProfile)
                                            <a href="{{ route('creators.show', $user->creatorProfile) }}" class="font-medium text-gray-900 hover:underline">{{ $user->name }}</a>
                                        @else
                                            <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                        @endif
                                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ __('Регистриран:') }} {{ $user->created_at->format('d.m.Y') }}

                                            @if ($isCreatorLike)
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

                                <div class="flex items-center gap-2">
                                    @if ($isCreatorLike)
                                        <a href="{{ route('admin.creators.edit', $user) }}">
                                            <x-secondary-button type="button">{{ __('Уреди профил') }}</x-secondary-button>
                                        </a>

                                        @if ($user->creatorProfile)
                                            <form method="POST" action="{{ route('messages.start', $user->creatorProfile) }}">
                                                @csrf
                                                <x-secondary-button type="submit">{{ __('Прати порака') }}</x-secondary-button>
                                            </form>
                                        @endif
                                    @elseif ($role === 'client')
                                        <a href="{{ route('clients.show', $user) }}">
                                            <x-secondary-button type="button">{{ __('Профил') }}</x-secondary-button>
                                        </a>
                                        <x-secondary-button type="button" x-data x-on:click="$dispatch('open-modal', 'confirm-convert-to-creator-{{ $user->id }}')">
                                            {{ __('Префрли во креативец') }}
                                        </x-secondary-button>
                                    @endif

                                    <x-danger-button type="button" x-data x-on:click="$dispatch('open-modal', 'confirm-delete-user-{{ $user->id }}')">
                                        {{ __('Избриши') }}
                                    </x-danger-button>
                                </div>

                                @if ($role === 'client')
                                    <x-modal name="confirm-convert-to-creator-{{ $user->id }}" focusable>
                                        <div class="p-6">
                                            <h2 class="text-lg font-medium text-gray-900">
                                                {{ __('Префрли го :name во креативец?', ['name' => $user->name]) }}
                                            </h2>

                                            <p class="mt-1 text-sm text-gray-600">
                                                {{ __('Корисникот ќе стане креативец и ќе треба да го помине онбордингот. Огласите што ги објавил како клиент остануваат недопрени.') }}
                                            </p>

                                            <div class="mt-6 flex justify-end gap-3">
                                                <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                                    {{ __('Откажи') }}
                                                </x-secondary-button>

                                                <form method="POST" action="{{ route('admin.users.convert-to-creator', $user) }}">
                                                    @csrf
                                                    <x-primary-button type="submit">{{ __('Да, префрли') }}</x-primary-button>
                                                </form>
                                            </div>
                                        </div>
                                    </x-modal>
                                @endif

                                <x-modal name="confirm-delete-user-{{ $user->id }}" focusable>
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Избриши го :name?', ['name' => $user->name]) }}
                                        </h2>

                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('Ова трајно ќе го избрише корисникот заедно со сите негови огласи, понуди, договори и пораки. Ова дејство не може да се врати.') }}
                                        </p>

                                        <div class="mt-6 flex justify-end gap-3">
                                            <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                                {{ __('Откажи') }}
                                            </x-secondary-button>

                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                                @csrf
                                                @method('DELETE')
                                                <x-danger-button type="submit">{{ __('Да, избриши') }}</x-danger-button>
                                            </form>
                                        </div>
                                    </div>
                                </x-modal>
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
