@php
    $categoryLabels = [
        'general' => __('Општо прашање'),
        'bug_report' => __('Пријава на бug'),
        'user_report' => __('Пријава на корисник'),
        'business' => __('Деловна соработка'),
    ];

    $statusLabels = [
        'new' => __('Нова'),
        'in_progress' => __('Во тек'),
        'resolved' => __('Решена'),
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Контакт пораки') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-md p-4 mb-6">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex gap-2 mb-6 border-b border-gray-200 flex-wrap">
                    <a href="{{ route('admin.contact-messages', ['status' => 'all']) }}"
                        class="px-4 py-2 text-sm font-medium border-b-2 {{ $status === 'all' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ __('Сите') }} ({{ $counts['all'] }})
                    </a>
                    <a href="{{ route('admin.contact-messages', ['status' => 'new']) }}"
                        class="px-4 py-2 text-sm font-medium border-b-2 {{ $status === 'new' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ __('Нова') }} ({{ $counts['new'] }})
                    </a>
                    <a href="{{ route('admin.contact-messages', ['status' => 'in_progress']) }}"
                        class="px-4 py-2 text-sm font-medium border-b-2 {{ $status === 'in_progress' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ __('Во тек') }} ({{ $counts['in_progress'] }})
                    </a>
                    <a href="{{ route('admin.contact-messages', ['status' => 'resolved']) }}"
                        class="px-4 py-2 text-sm font-medium border-b-2 {{ $status === 'resolved' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ __('Решена') }} ({{ $counts['resolved'] }})
                    </a>
                </div>

                @if ($messages->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('Нема контакт пораки во оваа категорија.') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach ($messages as $contactMessage)
                            <div class="border border-gray-200 rounded-md p-4">
                                <div class="flex items-start justify-between gap-4 flex-wrap">
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900">
                                            {{ $contactMessage->name }}
                                            <span class="text-xs font-semibold text-gray-500">· {{ $categoryLabels[$contactMessage->category] ?? $contactMessage->category }}</span>
                                        </p>
                                        <p class="text-sm text-gray-500">{{ $contactMessage->email }}</p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ $contactMessage->created_at->format('d.m.Y H:i') }}
                                            @if ($contactMessage->user)
                                                · {{ __('Регистриран корисник') }}
                                            @endif
                                        </p>
                                    </div>

                                    <form method="POST" action="{{ route('admin.contact-messages.update', $contactMessage) }}">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()"
                                            class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            @foreach ($statusLabels as $value => $label)
                                                <option value="{{ $value }}" {{ $contactMessage->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>

                                <p class="text-sm text-gray-700 mt-3 whitespace-pre-line">{{ $contactMessage->message }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $messages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
