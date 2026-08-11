@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'image' => null,
    'type' => 'website',
])

@php
    $description ??= __('CreatorSpot поврзува видеографи, фотографи, дизајнери, дигитални маркетери и едитори директно со клиенти низ Балканот.');
    $image ??= asset('images/shareImage.jpg');
    $pageTitle = $title ? "{$title} | CreatorSpot" : 'CreatorSpot — Where creators and clients meet';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $pageTitle }}</title>
        <meta name="description" content="{{ $description }}">
        @if ($keywords)
            <meta name="keywords" content="{{ $keywords }}">
        @endif
        <link rel="canonical" href="{{ url()->current() }}">

        <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">

        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $description }}">
        <meta property="og:type" content="{{ $type }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="CreatorSpot">
        <meta property="og:locale" content="{{ \App\Support\LocaleOptions::ogLocale(app()->getLocale()) }}">
        @if ($image)
            <meta property="og:image" content="{{ $image }}">
        @endif

        <meta name="twitter:card" content="{{ $image ? 'summary_large_image' : 'summary' }}">
        <meta name="twitter:title" content="{{ $pageTitle }}">
        <meta name="twitter:description" content="{{ $description }}">
        @if ($image)
            <meta name="twitter:image" content="{{ $image }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen" style="background-color:#F6F8FB;
            background-image:radial-gradient(circle, rgba(11,111,224,0.12) 1.4px, transparent 1.4px);
            background-size:22px 22px;">
            @auth
                @include('layouts.navigation')
            @else
                <x-public-nav />
            @endauth

            <!-- Page Heading -->
            @isset($header)
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                    <div style="width:64px;height:3px;border-radius:2px;margin-top:10px;
                        background:linear-gradient(90deg,#2D82E8,#0958B5);"></div>
                </div>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
