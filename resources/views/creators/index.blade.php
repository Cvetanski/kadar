@php
    $seoFilterCategoryIds = array_filter((array) request('category_ids', []));
    $seoFilterCategory = count($seoFilterCategoryIds) === 1
        ? $categories->firstWhere('id', (int) $seoFilterCategoryIds[array_key_first($seoFilterCategoryIds)])
        : null;
    $seoTitle = $seoFilterCategory ? $seoFilterCategory->name.' — '.__('Барај креативци') : __('Барај креативци');
    $seoDescription = $seoFilterCategory
        ? __('Пронајди верификувани').' '.mb_strtolower($seoFilterCategory->name).' '.__('креативци подготвени за твојот следен проект.')
        : __('Пребарувај и филтрирај верификувани креативци — видеографи, фотографи, дизајнери, дигитални маркетери и едитори.');
@endphp

<x-app-layout :title="$seoTitle" :description="$seoDescription">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Најди Креативци') }}</h2>
            @if (Auth::user()->role === 'client')
                <a href="{{ route('favorites.index') }}" class="text-sm text-indigo-600 hover:underline">{{ __('Зачувани креативци →') }}</a>
            @endif
        </div>
    </x-slot>

    @include('partials.creator-fancy-styles')
    @include('partials.browse-styles')

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="br-wrap">
                <livewire:browse-creators />
            </div>
        </div>
    </div>
</x-app-layout>
