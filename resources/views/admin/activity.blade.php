<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Активност') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-white border border-gray-200 rounded-md p-4">
                    <p class="text-2xl font-bold text-gray-900">{{ $totalConversations }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Вкупно разговори') }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-md p-4">
                    <p class="text-2xl font-bold text-gray-900">{{ $messagesToday }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Пораки денес') }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-md p-4">
                    <p class="text-2xl font-bold text-gray-900">{{ $messagesThisWeek }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Пораки оваа недела') }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-md p-4">
                    <p class="text-2xl font-bold text-gray-900">{{ $messagesTotal }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Пораки вкупно') }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-md p-4">
                    <p class="text-2xl font-bold text-gray-900">{{ $avgMessagesPerConversation }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Просечно пораки по разговор') }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex gap-2 mb-6 border-b border-gray-200">
                    <a href="{{ route('admin.activity', ['filter' => 'all']) }}"
                        class="px-4 py-2 text-sm font-medium border-b-2 {{ $filter === 'all' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ __('Сите') }} ({{ $totalConversations }})
                    </a>
                    <a href="{{ route('admin.activity', ['filter' => 'project']) }}"
                        class="px-4 py-2 text-sm font-medium border-b-2 {{ $filter === 'project' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ __('Преку проект') }} ({{ $projectConversationsCount }})
                    </a>
                    <a href="{{ route('admin.activity', ['filter' => 'direct']) }}"
                        class="px-4 py-2 text-sm font-medium border-b-2 {{ $filter === 'direct' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ __('Директни контакти') }} ({{ $directConversationsCount }})
                    </a>
                </div>

                @if ($recentConversations->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('Нема разговори во оваа категорија.') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-200">
                                    <th class="py-2 pr-4">{{ __('Учесници') }}</th>
                                    <th class="py-2 pr-4">{{ __('Тип') }}</th>
                                    <th class="py-2 pr-4">{{ __('Пораки') }}</th>
                                    <th class="py-2 pr-4">{{ __('Последна активност') }}</th>
                                    <th class="py-2 pr-4"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentConversations as $conversation)
                                    <tr class="border-b border-gray-100">
                                        <td class="py-3 pr-4">
                                            @forelse ($conversation->participants as $participant)
                                                <span class="inline-flex items-center gap-1 {{ ! $loop->last ? 'mr-2' : '' }}">
                                                    {{ $participant->name }}
                                                    <span class="text-xs text-gray-400">({{ match ($participant->role) {
                                                        'client' => __('клиент'),
                                                        'creator' => __('креативец'),
                                                        'admin' => __('админ'),
                                                        default => $participant->role,
                                                    } }})</span>
                                                </span>
                                            @empty
                                                <span class="text-gray-400">—</span>
                                            @endforelse
                                        </td>
                                        <td class="py-3 pr-4">
                                            @if ($conversation->project)
                                                <a href="{{ route('projects.show', $conversation->project) }}" class="text-indigo-600 hover:underline">
                                                    {{ \Illuminate\Support\Str::limit($conversation->project->title, 30) }}
                                                </a>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                    {{ __('Директен контакт') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 pr-4 text-gray-700">{{ $conversation->messages_count }}</td>
                                        <td class="py-3 pr-4 text-gray-500 text-xs">{{ $conversation->updated_at->format('d.m.Y H:i') }}</td>
                                        <td class="py-3 pr-4">
                                            <a href="{{ route('admin.activity.show', $conversation) }}" class="text-indigo-600 hover:underline text-xs font-medium">
                                                {{ __('Преглед →') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
