@php
    $seoFilterCategoryIds = array_filter((array) request('category_ids', []));
    $seoFilterCategory = count($seoFilterCategoryIds) === 1
        ? $categories->firstWhere('id', (int) $seoFilterCategoryIds[array_key_first($seoFilterCategoryIds)])
        : null;
    $seoTitle = $seoFilterCategory ? $seoFilterCategory->name.' — '.__('Барај креативци') : __('Барај креативци');
    $seoDescription = $seoFilterCategory
        ? __('Пронајди верификувани').' '.mb_strtolower($seoFilterCategory->name).' '.__('креативци подготвени за твојот следен проект.')
        : __('Пребарувај и филтрирај верификувани креативци — видеографи, фотографи, дизајнери, дигитални маркетери и едитори.');
    $seoKeywords = $seoFilterCategory
        ? mb_strtolower($seoFilterCategory->name).' Македонија, '.mb_strtolower($seoFilterCategory->name).' Скопје, hire '.mb_strtolower($seoFilterCategory->name).' Macedonia, freelance designer Macedonia, creative professionals Macedonia'
        : 'креативци Македонија, креативци Скопје, графички дизајнер Македонија, фотограф Македонија, видеограф Скопје, видео продукција Македонија, content creator Македонија, маркетер, freelance designer Macedonia, hire photographer Macedonia, hire videographer Macedonia, creative professionals Macedonia';
@endphp

<x-app-layout :title="$seoTitle" :description="$seoDescription" :keywords="$seoKeywords">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Најди Креативци') }}</h2>
            @if (Auth::user()?->role === 'client')
                <a href="{{ route('favorites.index') }}" class="text-sm text-indigo-600 hover:underline">{{ __('Зачувани креативци →') }}</a>
            @endif
        </div>
    </x-slot>

    @if ($seoFilterCategory)
        <script type="application/ld+json">
            {!! json_encode([
                '@@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => __('Барај Креативци'), 'item' => route('creators.index')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => $seoFilterCategory->name, 'item' => route('creators.index', ['category_ids' => [$seoFilterCategory->id]])],
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endif

    @include('partials.creator-fancy-styles')
    @include('partials.browse-styles')

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-md p-4 mb-6">{{ session('status') }}</div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 text-red-700 text-sm rounded-md p-4 mb-6">{{ session('error') }}</div>
            @endif

            <div class="br-wrap">
                <livewire:browse-creators />
            </div>
        </div>
    </div>
</x-app-layout>
