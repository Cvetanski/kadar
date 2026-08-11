<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex min-w-0">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <img src="{{ asset('images/logo2.svg') }}" alt="CreatorSpot" style="width:66px;height:66px;border-radius:6px;object-fit:contain;">
                        CreatorSpot
                        <span style="background:transparent;color:#D6249F;font-size:9px;font-weight:800;letter-spacing:0.06em;padding:2px 11px;border-radius:999px;text-transform:uppercase;border:1px solid #D6249F;">Beta</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 lg:-my-px lg:ms-10 lg:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Home') }}
                    </x-nav-link>

                    @if (Auth::user()->role === 'client')
                        <x-nav-link :href="route('creators.index')" :active="request()->routeIs('creators.index')">
                            {{ __('Барај креативци') }}
                        </x-nav-link>
                        <x-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.index')">
                            {{ __('Мои Огласи') }}
                        </x-nav-link>
                    @elseif (Auth::user()->role === 'creator')
                        <x-nav-link :href="route('projects.browse')" :active="request()->routeIs('projects.browse')">
                            {{ __('Најди Работа') }}
                        </x-nav-link>
                        <x-nav-link :href="route('proposals.index')" :active="request()->routeIs('proposals.index')">
                            {{ __('Мои апликации') }}
                        </x-nav-link>
                    @endif

                    @if (Auth::user()->is_admin)
                        <x-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users') || request()->routeIs('admin.verifications')">
                            {{ __('Корисници') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.contact-messages')" :active="request()->routeIs('admin.contact-messages')">
                            {{ __('Контакт пораки') }}
                        </x-nav-link>
                    @endif

                    <x-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.*')">
                        {{ __('Пораки') }}
                        <livewire:unread-badge wire:key="unread-badge-nav-link" class="ms-1" />
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden lg:flex lg:items-center lg:ms-6 gap-2 shrink-0">
                <a href="{{ route('messages.index') }}"
                    class="relative inline-flex items-center p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition ease-in-out duration-150">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.75a6 6 0 00-6-6 6 6 0 00-6 6v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    <livewire:unread-badge wire:key="unread-badge-icon" class="absolute -top-0.5 -right-0.5" />
                </a>

                <!-- Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <x-avatar :user="Auth::user()" size="w-7 h-7" textSize="text-xs" />
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if (Auth::user()->role === 'creator' && Auth::user()->creatorProfile)
                            <x-dropdown-link :href="route('creators.show', Auth::user()->creatorProfile)">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                        @else
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                        @endif

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Поставки') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('help')">
                            {{ __('Помош') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Logout') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>

                <x-language-switcher short />
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center lg:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Home') }}
            </x-responsive-nav-link>

            @if (Auth::user()->role === 'client')
                <x-responsive-nav-link :href="route('creators.index')" :active="request()->routeIs('creators.index')">
                    {{ __('Барај креативци') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.index')">
                    {{ __('Мои Огласи') }}
                </x-responsive-nav-link>
            @elseif (Auth::user()->role === 'creator')
                <x-responsive-nav-link :href="route('projects.browse')" :active="request()->routeIs('projects.browse')">
                    {{ __('Најди Работа') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('proposals.index')" :active="request()->routeIs('proposals.index')">
                    {{ __('Мои апликации') }}
                </x-responsive-nav-link>
            @endif

            @if (Auth::user()->is_admin)
                <x-responsive-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users') || request()->routeIs('admin.verifications')">
                    {{ __('Корисници') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.contact-messages')" :active="request()->routeIs('admin.contact-messages')">
                    {{ __('Контакт пораки') }}
                </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.*')">
                {{ __('Пораки') }} <livewire:unread-badge wire:key="unread-badge-mobile" class="ms-1" />
            </x-responsive-nav-link>

            <div class="px-4 mt-3">
                <x-language-switcher short />
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4 flex items-center gap-3">
                <x-avatar :user="Auth::user()" size="w-10 h-10" textSize="text-sm" />
                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                @if (Auth::user()->role === 'creator' && Auth::user()->creatorProfile)
                    <x-responsive-nav-link :href="route('creators.show', Auth::user()->creatorProfile)">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                @endif

                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Поставки') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('help')">
                    {{ __('Помош') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Logout') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
