<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.activity') }}" class="text-sm text-indigo-600 hover:underline">{{ __('← Назад кон активност') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h2 class="font-semibold text-lg text-gray-900 mb-3">{{ __('Разговор') }}</h2>

                <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                    <div>
                        <span class="text-gray-500">{{ __('Учесници:') }}</span>
                        @forelse ($conversation->participants as $participant)
                            <span class="font-medium text-gray-900">
                                {{ $participant->name }}
                                <span class="text-xs text-gray-400 font-normal">({{ match ($participant->role) {
                                    'client' => __('клиент'),
                                    'creator' => __('креативец'),
                                    'admin' => __('админ'),
                                    default => $participant->role,
                                } }})</span>
                            </span>{{ ! $loop->last ? ',' : '' }}
                        @empty
                            <span class="text-gray-400">—</span>
                        @endforelse
                    </div>

                    <div>
                        <span class="text-gray-500">{{ __('Тип:') }}</span>
                        @if ($conversation->project)
                            <a href="{{ route('projects.show', $conversation->project) }}" class="text-indigo-600 hover:underline font-medium">
                                {{ $conversation->project->title }}
                            </a>
                        @else
                            <span class="font-medium text-gray-900">{{ __('Директен контакт') }}</span>
                        @endif
                    </div>

                    <div>
                        <span class="text-gray-500">{{ __('Пораки:') }}</span>
                        <span class="font-medium text-gray-900">{{ $messages->count() }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if ($messages->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('Нема пораки во овој разговор.') }}</p>
                @else
                    <div class="space-y-4">
                        @foreach ($messages as $message)
                            <div class="border-b border-gray-100 pb-4 last:border-b-0 last:pb-0">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $message->sender?->name ?? __('Непознат корисник') }}
                                        @if ($message->type !== 'user')
                                            <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                {{ $message->type }}
                                            </span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $message->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                                <p class="text-sm text-gray-700" style="white-space:pre-line;">{{ $message->body }}</p>
                                @if ($message->attachment_url)
                                    <a href="{{ $message->attachment_url }}" target="_blank" rel="noopener" class="text-xs text-indigo-600 hover:underline">
                                        {{ __('📎 Прилог') }}
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
