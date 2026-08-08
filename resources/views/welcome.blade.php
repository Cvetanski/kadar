<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>CreatorSpot — Where creators and clients meet</title>
<meta name="description" content="{{ __('CreatorSpot поврзува видеографи, фотографи, дизајнери, дигитални маркетери и едитори директно со клиенти низ Балканот. Опиши го проектот, добиј понуди за неколку часа.') }}">
<link rel="canonical" href="{{ url()->current() }}">

<link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">

<meta property="og:title" content="CreatorSpot — Where creators and clients meet">
<meta property="og:description" content="{{ __('CreatorSpot поврзува видеографи, фотографи, дизајнери, дигитални маркетери и едитори директно со клиенти низ Балканот.') }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="CreatorSpot">
<meta property="og:locale" content="{{ \App\Support\LocaleOptions::ogLocale(app()->getLocale()) }}">
<meta property="og:image" content="{{ asset('images/shareImage.jpg') }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="CreatorSpot — Where creators and clients meet">
<meta name="twitter:description" content="{{ __('CreatorSpot поврзува видеографи, фотографи, дизајнери, дигитални маркетери и едитори директно со клиенти низ Балканот.') }}">
<meta name="twitter:image" content="{{ asset('images/shareImage.jpg') }}">

<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "Organization",
    "name": "CreatorSpot",
    "url": "{{ route('welcome') }}",
    "description": {!! json_encode(__('CreatorSpot поврзува видеографи, фотографи, дизајнери, дигитални маркетери и едитори директно со клиенти низ Балканот.')) !!}
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "WebSite",
    "name": "CreatorSpot",
    "url": "{{ route('welcome') }}",
    "potentialAction": {
        "@type": "SearchAction",
        "target": {
            "@type": "EntryPoint",
            "urlTemplate": "{{ route('creators.index') }}?search={search_term_string}"
        },
        "query-input": "required name=search_term_string"
    }
}
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{
    color-scheme:light;
    --bg:#FFFFFF;
    --bg-soft:#F6F8FB;
    --surface:#FFFFFF;
    --border:#E8EBF0;
    --text:#14171F;
    --text-dim:#666B76;
    --text-faint:#9AA0AB;
    --blue:#0B6FE0;
    --blue-dark:#0958B5;
    --blue-soft:#EAF2FE;
    --green:#17A673;
    --green-soft:#E7F8F1;
    --amber:#F5A524;
    --shadow-sm:0 1px 2px rgba(20,23,31,0.05), 0 1px 1px rgba(20,23,31,0.04);
    --shadow-md:0 8px 24px rgba(20,23,31,0.08), 0 2px 6px rgba(20,23,31,0.04);
    --shadow-lg:0 20px 50px rgba(20,23,31,0.12), 0 4px 12px rgba(20,23,31,0.06);
    --radius:16px;
    --font:'Inter', sans-serif;
  }
  *{margin:0;padding:0;box-sizing:border-box;}
  select,option{background:#fff;color:var(--text);}
  html{scroll-behavior:smooth;overflow-x:hidden;}
  body{
    background:var(--bg);color:var(--text);font-family:var(--font);
    -webkit-font-smoothing:antialiased;
    background-image:radial-gradient(circle, rgba(20,23,31,0.12) 1.4px, transparent 1.4px);
    background-size:22px 22px;
    overflow-x:hidden;max-width:100vw;
  }
  a{text-decoration:none;color:inherit;}
  ::selection{background:var(--blue-soft);color:var(--blue-dark);}
  a:focus-visible, button:focus-visible{outline:2px solid var(--blue);outline-offset:2px;}
  .wrap{max-width:1160px;margin:0 auto;padding:0 32px;}

  {{-- Nav CSS (nav, .logo, .nav-links, .nav-cta, .nav-burger, .nav-mobile-*) now lives
       inside the <x-public-nav /> component itself, since that component is also used
       on pages outside welcome.blade.php (e.g. the public creator profile) that don't
       load this <style> block. --}}

  .btn{display:inline-flex;align-items:center;gap:8px;font-family:var(--font);
    font-weight:600;font-size:14px;padding:10px 18px;border-radius:10px;
    border:1px solid transparent;cursor:pointer;transition:all .15s ease;}
  .btn-primary{background:linear-gradient(135deg, #2D82E8, #0958B5);color:#fff;box-shadow:var(--shadow-sm);border:none;background-clip:padding-box;}
  .btn-primary:hover{background:linear-gradient(135deg, #1F72D8, #0847A0);transform:translateY(-1px);box-shadow:var(--shadow-md);}
  .btn-ghost{color:var(--text);border-color:var(--border);background:#fff;}
  .btn-ghost:hover{border-color:var(--text-faint);}
  .btn-lg{padding:14px 24px;font-size:15px;border-radius:12px;}

  .hero-wrap{max-width:1160px;margin:0 auto;padding:24px 16px;}
  .hero-frame{
    position:relative;border-radius:24px;overflow:hidden;min-height:540px;
    display:flex;flex-direction:column;justify-content:center;
    box-shadow:var(--shadow-lg);border:1px solid var(--border);
  }
  .hero-bg{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center 30%;}
  .hero-scrim{
    position:absolute;inset:0;
    background:linear-gradient(90deg, rgba(6,10,22,0.82) 0%, rgba(7,12,26,0.55) 45%, rgba(8,14,28,0.15) 75%);
  }

  .hero-content{position:relative;z-index:2;padding:44px 56px;max-width:760px;}
  h1{font-size:clamp(34px,4.8vw,52px);font-weight:800;letter-spacing:-0.03em;line-height:1.06;color:#fff;}
  h1 .blue{background:linear-gradient(135deg,#5CA8F5,#8FC4FF);-webkit-background-clip:text;background-clip:text;color:transparent;}
  .hero-sub{margin:20px 0 0;font-size:16.5px;color:rgba(255,255,255,0.85);max-width:460px;line-height:1.6;font-weight:500;}

  .role-toggle{display:inline-flex;border:1.5px solid rgba(255,255,255,0.45);border-radius:999px;
    padding:4px;margin-top:104px;gap:2px;}
  .role-toggle button{
    border:none;background:transparent;color:rgba(255,255,255,0.85);font-family:var(--font);
    font-weight:600;font-size:14px;padding:10px 24px;border-radius:999px;cursor:pointer;transition:all .15s ease;
  }
  .role-toggle button.active{background:#fff;color:var(--text);}

  .search-bar{
    display:flex;align-items:center;gap:8px;background:#fff;
    border-radius:12px;padding:4px 4px 4px 16px;margin-top:16px;max-width:520px;box-shadow:var(--shadow-md);
  }
  .search-bar input{flex:1;border:none;outline:none;font-family:var(--font);font-size:14.5px;background:transparent;color:var(--text);}
  .search-bar input::placeholder{color:var(--text-faint);}
  .search-bar button{
    display:flex;align-items:center;gap:8px;background:var(--text);color:#fff;border:none;border-radius:9px;
    padding:9px 20px;font-weight:700;font-size:13.5px;cursor:pointer;font-family:var(--font);
    transition:transform .15s ease;white-space:nowrap;
  }
  .search-bar button:hover{transform:translateY(-1px);background:#000;}

  .hero-sub-short{display:none;}

  .quick-tags{display:flex;gap:10px;margin-top:18px;flex-wrap:wrap;}
  .quick-tags a{display:inline-flex;align-items:center;gap:6px;font-size:12.5px;font-weight:600;color:#fff;
    border:1.5px solid rgba(255,255,255,0.4);padding:8px 15px;border-radius:999px;transition:all .15s ease;}
  .quick-tags a:hover{border-color:#fff;background:rgba(255,255,255,0.1);}

  @media(max-width:760px){
    .hero-wrap{padding:16px;}
    .hero-frame{min-height:520px;}
    .hero-content{padding:36px 24px;}
    .hero-scrim{background:linear-gradient(90deg, rgba(8,14,28,0.02) 0%, rgba(7,12,26,0.45) 55%, rgba(6,10,22,0.88) 100%);}
    .role-toggle{margin-top:96px;border:none;background:rgba(20,23,31,0.55);backdrop-filter:blur(6px);}
    .search-bar button{border-radius:50%;width:40px;height:40px;padding:0;justify-content:center;}
    .search-bar .search-btn-label{display:none;}

    .hero-sub-full{display:none;}
    .hero-sub-short{display:block;}
  }

  .section{max-width:1160px;margin:0 auto;padding:110px 32px;}
  .section-eyebrow{font-size:13px;font-weight:700;color:var(--blue);letter-spacing:0.02em;
    text-transform:uppercase;margin-bottom:12px;text-align:center;}
  .section-title{font-size:clamp(28px,4vw,40px);font-weight:800;letter-spacing:-0.02em;
    text-align:center;max-width:640px;margin:0 auto;}
  .section-sub{text-align:center;color:var(--text-dim);font-size:16px;max-width:480px;
    margin:16px auto 0;}

  .cat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:56px;}
  .cat-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);
    padding:26px;transition:box-shadow .2s ease, transform .2s ease;display:block;}
  .cat-card:hover{box-shadow:var(--shadow-md);transform:translateY(-3px);}
  .cat-icon{width:40px;height:40px;border-radius:11px;display:flex;align-items:center;
    justify-content:center;font-size:18px;margin-bottom:16px;}
  .cat-icon.i1{background:var(--blue-soft);}
  .cat-icon.i2{background:var(--green-soft);}
  .cat-icon.i3{background:#FDF0E0;}
  .cat-icon.i4{background:#EAF2FE;}
  .cat-icon.i5{background:#FBEAF0;}
  .cat-icon.i6{background:#EAF6EE;}
  .cat-name{font-weight:700;font-size:16px;margin-bottom:8px;}
  .cat-desc{font-size:13.5px;color:var(--text-dim);line-height:1.55;}
  @media(max-width:700px){.cat-grid{grid-template-columns:repeat(2,1fr);}}
  @media(max-width:460px){.cat-grid{grid-template-columns:1fr;}}

  .creator-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:56px;}
  .creator-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);
    padding:20px;transition:box-shadow .2s ease, transform .2s ease;display:block;}
  .creator-card:hover{box-shadow:var(--shadow-md);transform:translateY(-3px);}
  .cc-top{display:flex;gap:12px;align-items:center;margin-bottom:14px;}
  .cc-avatar{width:48px;height:48px;border-radius:12px;flex-shrink:0;}
  .cc-a1{background:linear-gradient(135deg,#0B6FE0,#7CC4FF);}
  .cc-a2{background:linear-gradient(135deg,#17A673,#7BE0BC);}
  .cc-a3{background:linear-gradient(135deg,#F5A524,#FBD08A);}
  .cc-a4{background:linear-gradient(135deg,#0B6FE0,#17A673);}
  .cc-a5{background:linear-gradient(135deg,#EC4899,#F9A8D4);}
  .cc-a6{background:linear-gradient(135deg,#0B6FE0,#5CA8F5);}
  .cc-name{font-weight:700;font-size:15px;}
  .cc-role{font-size:12.5px;color:var(--blue);font-weight:600;margin-top:2px;}
  .cc-loc{font-size:12.5px;color:var(--text-faint);margin-top:1px;}
  .cc-bottom{display:flex;justify-content:space-between;align-items:center;
    border-top:1px solid var(--border);padding-top:14px;}
  .cc-stars{font-size:13px;color:var(--amber);}
  .cc-count{color:var(--text-faint);font-weight:500;}
  .cc-rate{font-weight:700;font-size:14px;}
  .cc-empty{text-align:center;color:var(--text-dim);font-size:15px;padding:40px 0;grid-column:1/-1;}
  @media(max-width:860px){.creator-grid{grid-template-columns:repeat(2,1fr);}}
  @media(max-width:560px){.creator-grid{grid-template-columns:1fr;}}

  .flow-wrap{display:grid;grid-template-columns:repeat(3,1fr);gap:0;margin-top:64px;position:relative;}
  .flow-step{text-align:center;padding:0 20px;position:relative;}
  .flow-num{width:36px;height:36px;border-radius:10px;background:var(--blue);color:#fff;
    font-weight:700;font-size:14px;display:flex;align-items:center;justify-content:center;
    margin:0 auto 18px;}
  .flow-title{font-weight:700;font-size:16px;margin-bottom:8px;}
  .flow-desc{font-size:13.5px;color:var(--text-dim);line-height:1.55;max-width:220px;margin:0 auto;}
  .flow-arrow{position:absolute;top:18px;right:-14px;color:var(--text-faint);font-size:18px;}
  @media(max-width:760px){.flow-wrap{grid-template-columns:1fr;gap:36px;}.flow-arrow{display:none;}}

  .stat-band{background:var(--bg-soft);border-radius:24px;max-width:1096px;margin:0 auto;
    padding:56px 32px;display:grid;grid-template-columns:repeat(4,1fr);gap:24px;text-align:center;}
  .stat-num{font-size:32px;font-weight:800;color:var(--blue);}
  .stat-label{font-size:13px;color:var(--text-dim);margin-top:6px;font-weight:500;}
  @media(max-width:700px){.stat-band{grid-template-columns:repeat(2,1fr);}}

  .founder-card{max-width:760px;margin:56px auto 0;background:var(--surface);border:1px solid var(--border);
    border-radius:var(--radius);padding:44px 48px;text-align:center;box-shadow:var(--shadow-md);}
  .founder-avatar{width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#0B6FE0,#7CC4FF);
    color:#fff;font-weight:800;font-size:20px;display:flex;align-items:center;justify-content:center;
    margin:0 auto 20px;overflow:hidden;}
  .founder-avatar img{width:100%;height:100%;object-fit:cover;}
  .founder-text{font-size:14px;line-height:1.65;color:var(--text-dim);}
  .founder-sig{margin-top:20px;padding-top:16px;border-top:1px solid var(--border);
    font-weight:700;font-size:13px;color:var(--text);}
  @media(max-width:600px){.founder-card{padding:32px 24px;}}
  .founder-cta{margin-top:24px;}

  .meet-overlay{display:none;position:fixed;inset:0;background:rgba(20,23,31,.5);z-index:100;
    align-items:center;justify-content:center;padding:20px;}
  .meet-overlay.is-open{display:flex;}
  .meet-modal{background:var(--surface);border-radius:var(--radius);max-width:440px;width:100%;
    padding:32px;position:relative;box-shadow:var(--shadow-md);max-height:90vh;overflow-y:auto;}
  .meet-close{position:absolute;top:16px;right:16px;background:none;border:none;font-size:20px;
    color:var(--text-faint);cursor:pointer;line-height:1;padding:4px;}
  .meet-close:hover{color:var(--text);}
  .meet-modal h3{font-size:20px;font-weight:800;margin:0 0 6px;}
  .meet-modal p.meet-sub{color:var(--text-dim);font-size:13.5px;margin:0 0 22px;}
  .meet-field{margin-bottom:14px;}
  .meet-field label{display:block;font-size:12.5px;font-weight:700;color:var(--text-dim);margin-bottom:6px;}
  .meet-field input,.meet-field textarea{
    width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:10px;
    font-family:var(--font);font-size:13.5px;background:var(--bg-soft);box-sizing:border-box;
  }
  .meet-field input:focus,.meet-field textarea:focus{outline:2px solid var(--blue);outline-offset:1px;background:#fff;}
  .meet-row{display:flex;gap:12px;}
  .meet-row .meet-field{flex:1;}
  .meet-error{color:#DC2626;font-size:12px;margin-top:4px;}
  .meet-website{position:absolute;left:-9999px;}
  .meet-submit{width:100%;justify-content:center;margin-top:6px;}
  .meet-status{margin-top:16px;font-size:13.5px;text-align:center;padding:12px;border-radius:10px;}
  .meet-status.is-success{background:var(--green-soft);color:#0F7A54;}
  .meet-status.is-error{background:#FEE2E2;color:#DC2626;}

  .cta-band{background:var(--bg-soft);border-radius:24px;max-width:1096px;margin:0 auto 110px;
    padding:80px 32px;text-align:center;border:1px solid var(--border);}
  .cta-band h2{font-size:clamp(28px,4.5vw,42px);font-weight:800;letter-spacing:-0.02em;
    max-width:560px;margin:0 auto 16px;}
  .cta-band p{color:var(--text-dim);font-size:16px;max-width:440px;margin:0 auto 32px;}
  .cta-buttons{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;}
  .cta-checks{display:flex;justify-content:center;gap:24px;margin-top:24px;flex-wrap:wrap;}
  .cta-check{font-size:12.5px;color:var(--text-dim);display:flex;align-items:center;gap:6px;}

  footer{max-width:1160px;margin:0 auto;padding:0 32px 60px;
    display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;
    color:var(--text-faint);font-size:13px;}
  footer .flinks{display:flex;gap:22px;}
  footer a:hover{color:var(--text-dim);}
</style>
</head>
<body>

<x-public-nav />

<div class="hero-wrap">
  <div class="hero-frame">
    <picture>
      <source media="(max-width:760px)" srcset="{{ asset('images/hero-team-mobile.webp') }}">
      <img class="hero-bg" src="{{ asset('images/hero-team.webp') }}" alt="{{ __('Тим на креативци и клиенти на CreatorSpot') }}">
    </picture>
    <div class="hero-scrim"></div>

    <div class="hero-content">
      <h1>{{ __('Секој проект заслужува') }}<br><span class="blue">{{ __('вистински талент.') }}</span></h1>
      <p class="hero-sub hero-sub-full">{{ __('CreatorSpot поврзува видеографи, фотографи, дизајнери, дигитални маркетери, содржина креатори и едитори директно со клиенти.') }}</p>
      <p class="hero-sub hero-sub-short">{{ __('CreatorSpot поврзува креативци со клиенти.') }}</p>

      <div class="role-toggle">
        <button type="button" class="active" id="roleClientBtn" onclick="setHeroRole('client')">{{ __('Барам креативец') }}</button>
        <button type="button" id="roleCreatorBtn" onclick="setHeroRole('creator')">{{ __('Сум креативец') }}</button>
      </div>

      <form class="search-bar" id="heroSearchForm" action="{{ route('creators.index') }}" method="GET">
        <input type="text" name="search" id="heroSearchInput" placeholder="{{ __('Опиши што ти треба...') }}">
        <button type="submit" aria-label="{{ __('Барај') }}">🔍 <span class="search-btn-label">{{ __('Барај') }}</span></button>
      </form>

      <div class="quick-tags" id="heroQuickTags">
        @foreach ($categories as $category)
          <a href="{{ Auth::check() ? route('creators.index', ['category_ids' => [$category->id]]) : route('register', ['role' => 'client']) }}"
            data-creator-href="{{ Auth::check() ? route('creators.index', ['category_ids' => [$category->id]]) : route('register', ['role' => 'client']) }}"
            data-project-href="{{ Auth::check() ? route('projects.browse', ['categoryIds' => [$category->id]]) : route('register', ['role' => 'creator']) }}">{{ $category->name }} →</a>
        @endforeach
      </div>

      @auth
        <a href="{{ route('dashboard') }}" class="btn btn-primary" style="margin-top:20px;">{{ __('Оди на контролна табла →') }}</a>
      @else
        <a href="{{ route('register') }}" class="btn btn-primary" style="margin-top:20px;" id="heroRegisterBtn">{{ __('Регистрирај се бесплатно →') }}</a>
      @endauth
    </div>
  </div>
</div>

<section class="section" id="{{ \App\Support\AnchorSlugs::for('kategorii') }}">
  <div class="section-eyebrow">{{ __('Категории') }}</div>
  <div class="section-title">{{ __('6 фокуси, еден стандард за квалитет') }}</div>
  <p class="section-sub">{{ __('Секоја категорија има сопствен процес на верификација на портфолио пред да се објави профилот.') }}</p>
  <div class="cat-grid">
    @foreach ($categories as $category)
      <a href="{{ Auth::check() ? route('creators.index', ['category_ids' => [$category->id]]) : route('register', ['role' => 'client']) }}" class="cat-card">
        <div class="cat-icon i{{ ($loop->index % 6) + 1 }}">{{ $category->icon }}</div>
        <div class="cat-name">{{ $category->name }}</div>
        <div class="cat-desc">{{ $category->description }}</div>
      </a>
    @endforeach
  </div>
</section>

<section class="section" id="{{ \App\Support\AnchorSlugs::for('kreativci') }}" style="background:var(--bg-soft);border-radius:32px;max-width:1096px;">
  <div class="section-eyebrow">{{ __('Во фокус') }}</div>
  <div class="section-title">{{ __('Препорачани креативци') }}</div>
  <p class="section-sub">{{ __('Профили верификувани преку портфолио и претходни клиенти.') }}</p>
  <div class="creator-grid">
    @forelse ($creators as $creator)
      <a href="{{ route('creators.show', $creator) }}" class="creator-card">
        <div class="cc-top">
          @if ($creator->user->avatar_url)
            <img src="{{ $creator->user->avatar_url }}" alt="{{ $creator->user->name }}" class="cc-avatar" style="object-fit:cover;" loading="lazy">
          @else
            <div class="cc-avatar {{ $creator->avatarClass }}" style="display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:16px;">{{ $creator->user->initials }}</div>
          @endif
          <div>
            <div class="cc-name">{{ $creator->user->name }}</div>
            <div class="cc-role">{{ $creator->categories->first()?->name ?? $creator->headline }}</div>
            <div class="cc-loc">{{ $creator->remote_ok ? __('Remote') : ($creator->user->city?->name ?? $creator->user->country?->name ?? '—') }}</div>
          </div>
        </div>
        <div class="cc-bottom">
          @if ($creator->reviewCount > 0)
            <div class="cc-stars">{{ str_repeat('★', $creator->avgRating) }}{{ str_repeat('☆', 5 - $creator->avgRating) }} <span class="cc-count">({{ $creator->reviewCount }})</span></div>
          @else
            <div class="cc-stars" style="color:var(--text-faint);font-weight:600;">{{ __('Нов') }}</div>
          @endif
          <div class="cc-rate">{{ $creator->hourly_rate ? $creator->hourly_rate.'€/ч' : '—' }}</div>
        </div>
      </a>
    @empty
      <div class="cc-empty">{{ __('Наскоро првите верификувани креативци на платформата — биди меѓу првите!') }}</div>
    @endforelse
  </div>
</section>

<section class="section" id="{{ \App\Support\AnchorSlugs::for('kako') }}">
  <div class="section-eyebrow">{{ __('Процес') }}</div>
  <div class="section-title">{{ __('Три чекори. Ништо повеќе.') }}</div>
  <p class="section-sub">{{ __('Од идеја до готов проект за денови, не недели, со целосна контрола над секој чекор.') }}</p>
  <div class="flow-wrap">
    <div class="flow-step">
      <div class="flow-num">1</div>
      <div class="flow-title">{{ __('Опиши го проектот') }}</div>
      <div class="flow-desc">{{ __('Буџет, рок и стил — платформата предлага соодветни креативци веднаш.') }}</div>
      <div class="flow-arrow">→</div>
    </div>
    <div class="flow-step">
      <div class="flow-num">2</div>
      <div class="flow-title">{{ __('Избери и ангажирај') }}</div>
      <div class="flow-desc">{{ __('Спореди профили, портфолија и цени, па ангажирај со еден клик.') }}</div>
      <div class="flow-arrow">→</div>
    </div>
    <div class="flow-step">
      <div class="flow-num">3</div>
      <div class="flow-title">{{ __('Прими и оцени') }}</div>
      <div class="flow-desc">{{ __('Одобри ја испораката и остави оценка за креативецот') }}</div>
    </div>
  </div>
</section>

{{-- Stat band (активни креативци / завршени проекти / оцена / време на одговор) е привремено скриен
     додека немаме доволно реални податоци за да изгледа веродостојно. Кога verified creators,
     completed contracts и reviews нараснат до разумни бројки, врати ја оваа секција со реални
     пресметки (CreatorProfile::where('verified', true)->count(), Contract::where('status','completed')->count(),
     Review::avg('rating')). --}}

{{-- "За нас" секцијата (+ придружниот "закажи состанок" modal) е привремено исклучена на барање.
     За да се врати: одмотај го овој коментар и врати го "За нас" линкот во public-nav.blade.php,
     плус ги врати ги openMeetingModal/closeMeetingModal + нивните event listeners во <script> подолу. --}}
{{--
<section class="section" id="zanas">
  <div class="section-eyebrow">{{ __('За нас') }}</div>
  <div class="section-title">{{ __('Изградено од креативец, за креативци') }}</div>
  <p class="section-sub">{{ __('CreatorSpot не е уште една платформа од табела, туку одговор на проблем што основачот лично го живееше.') }}</p>
  <div class="founder-card">
    <div class="founder-avatar"><img src="{{ asset('images/cvetanski.jpg') }}" alt="Cvetanskifootage"></div>
    <p class="founder-text">{{ __('Основачот на CreatorSpot, Cvetanskifootage, самиот работеше како видео креативец и знаеше од прва рака колку е тешко да се најдат вистински клиенти на почетокот, со часови бараење, несигурни договори и изгубено време. Кога сфати дека истата борба ја живеат стотици други креативци и клиенти низ регионот, реши да ја изгради платформата што самиот ја бараше: место каде креативците лесно ги наоѓаат вистинските клиенти, а клиентите ги наоѓаат вистинските креативци.') }}</p>
    <p class="founder-sig">Cvetanskifootage, {{ __('основач на CreatorSpot') }}</p>
    <div class="founder-cta">
      <button type="button" class="btn btn-primary" onclick="openMeetingModal()">{{ __('Закажи состанок со основачот') }}</button>
    </div>
  </div>
</section>

<div class="meet-overlay" id="meetOverlay">
  <div class="meet-modal">
    <button type="button" class="meet-close" onclick="closeMeetingModal()" aria-label="{{ __('Затвори') }}">✕</button>
    <h3>{{ __('Закажи состанок со основачот') }}</h3>
    <p class="meet-sub">{{ __('Одбери термин што ти одговара, потврда ќе добиеш на email.') }}</p>

    <form id="meetForm">
      @csrf
      <input type="text" name="website" class="meet-website" tabindex="-1" autocomplete="off">

      <div class="meet-field">
        <label for="meetName">{{ __('Име') }}</label>
        <input type="text" id="meetName" name="name" required maxlength="100">
      </div>

      <div class="meet-field">
        <label for="meetEmail">Email</label>
        <input type="email" id="meetEmail" name="email" required maxlength="255">
      </div>

      <div class="meet-row">
        <div class="meet-field">
          <label for="meetDate">{{ __('Датум') }}</label>
          <input type="date" id="meetDate" name="date" required>
        </div>
        <div class="meet-field">
          <label for="meetTime">{{ __('Време') }}</label>
          <input type="time" id="meetTime" name="time" required>
        </div>
      </div>

      <div class="meet-field">
        <label for="meetNote">{{ __('Порака (опционално)') }}</label>
        <textarea id="meetNote" name="note" rows="3" maxlength="1000"></textarea>
      </div>

      <button type="submit" class="btn btn-primary meet-submit">{{ __('Испрати барање') }}</button>
      <div id="meetStatus"></div>
    </form>
  </div>
</div>
--}}

<script>
  function setHeroRole(role){
    var isCreator = role === 'creator';
    document.getElementById('roleClientBtn').classList.toggle('active', !isCreator);
    document.getElementById('roleCreatorBtn').classList.toggle('active', isCreator);

    var form = document.getElementById('heroSearchForm');
    var input = document.getElementById('heroSearchInput');
    form.action = isCreator ? @json(route('projects.browse')) : @json(route('creators.index'));
    input.placeholder = isCreator
      ? @json(__('Пребарај огласи по наслов или опис...'))
      : @json(__('Опиши што ти треба...'));

    document.querySelectorAll('#heroQuickTags a').forEach(function (a) {
      a.href = isCreator ? a.dataset.projectHref : a.dataset.creatorHref;
    });

    var registerBtn = document.getElementById('heroRegisterBtn');
    if (registerBtn) {
      registerBtn.href = isCreator
        ? @json(route('register', ['role' => 'creator']))
        : @json(route('register', ['role' => 'client']));
    }
  }

  /* Закажи-состанок modal логиката е исклучена заедно со "За нас" секцијата (види ја HTML
     секцијата погоре) — самиот елемент #meetOverlay/#meetForm не постои во DOM додека е
     исклучено, па овие listener-и остануваат закоментирани за да не фрлат JS грешка.
  function openMeetingModal(){
    document.getElementById('meetOverlay').classList.add('is-open');
    document.getElementById('meetDate').min = new Date().toISOString().split('T')[0];
  }
  function closeMeetingModal(){
    document.getElementById('meetOverlay').classList.remove('is-open');
  }
  document.getElementById('meetOverlay').addEventListener('click', function (e) {
    if (e.target === this) closeMeetingModal();
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeMeetingModal();
  });

  document.getElementById('meetForm').addEventListener('submit', function (e) {
    e.preventDefault();

    var form = e.target;
    var statusEl = document.getElementById('meetStatus');
    var submitBtn = form.querySelector('.meet-submit');
    var token = form.querySelector('[name=_token]').value;

    statusEl.className = '';
    statusEl.textContent = '';
    submitBtn.disabled = true;

    fetch('{{ route('meeting-request.store') }}', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json',
      },
      body: new FormData(form),
    })
      .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
      .then(function (result) {
        if (result.ok) {
          statusEl.className = 'meet-status is-success';
          statusEl.textContent = result.data.message;
          form.reset();
          setTimeout(closeMeetingModal, 2500);
        } else {
          var messages = result.data.errors
            ? Object.values(result.data.errors).flat().join(' ')
            : (result.data.message || @json(__('Настана грешка. Обиди се повторно.')));
          statusEl.className = 'meet-status is-error';
          statusEl.textContent = messages;
        }
      })
      .catch(function () {
        statusEl.className = 'meet-status is-error';
        statusEl.textContent = @json(__('Настана грешка. Обиди се повторно.'));
      })
      .finally(function () {
        submitBtn.disabled = false;
      });
  });
  */
</script>

<div class="cta-band">
  <h2>{{ __('Твојот следен проект чека вистински кадар.') }}</h2>
  <p>{{ __('Придружи се на платформата што ги поврзува најдобрите креативци со клиенти што ја ценат работата.') }}</p>
  <div class="cta-buttons">
    @auth
      <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">{{ __('Оди на контролна табла →') }}</a>
    @else
      <a href="{{ route('register') }}" class="btn btn-primary btn-lg">{{ __('Регистрирај се бесплатно →') }}</a>
    @endauth
    <a href="#{{ \App\Support\AnchorSlugs::for('kreativci') }}" class="btn btn-ghost btn-lg">{{ __('Најди Креативци') }}</a>
  </div>
  <div class="cta-checks">
    <div class="cta-check">✓ {{ __('Без картичка') }}</div>
    <div class="cta-check">✓ {{ __('Откажи кога сакаш') }}</div>
    <div class="cta-check">✓ {{ __('Ти одобруваш секој ангажман') }}</div>
  </div>
</div>

<footer>
  <div class="logo"><img src="{{ asset('images/logo2.svg') }}" alt="CreatorSpot" style="width:66px;height:66px;border-radius:6px;object-fit:contain;">CreatorSpot<span style="background:transparent;color:#D6249F;font-size:9px;font-weight:800;letter-spacing:0.06em;padding:2px 11px;border-radius:999px;text-transform:uppercase;border:1px solid #D6249F;">Beta</span></div>
  <div class="flinks">
    <a href="{{ route('legal.terms') }}" target="_blank" rel="noopener">{{ __('Услови') }}</a><a href="{{ route('legal.privacy') }}" target="_blank" rel="noopener">{{ __('Приватност') }}</a><a href="{{ route('contact.create') }}">{{ __('Контакт') }}</a>
  </div>
  <div>© {{ date('Y') }} CreatorSpot</div>
</footer>

</body>
</html>
