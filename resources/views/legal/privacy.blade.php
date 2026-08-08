<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ __('Политика за приватност') }} — CreatorSpot</title>
<meta name="description" content="{{ __('Политиката за приватност на CreatorSpot — како ги обработуваме твоите податоци на платформата.') }}">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:title" content="{{ __('Политика за приватност') }} — CreatorSpot">
<meta property="og:description" content="{{ __('Политиката за приватност на CreatorSpot — како ги обработуваме твоите податоци на платформата.') }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="CreatorSpot">
<meta property="og:locale" content="{{ \App\Support\LocaleOptions::ogLocale(app()->getLocale()) }}">
<meta property="og:image" content="{{ asset('images/shareImage.jpg') }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ __('Политика за приватност') }} — CreatorSpot">
<meta name="twitter:description" content="{{ __('Политиката за приватност на CreatorSpot — како ги обработуваме твоите податоци на платформата.') }}">
<meta name="twitter:image" content="{{ asset('images/shareImage.jpg') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{
    color-scheme:light;
    --bg:#FFFFFF; --bg-soft:#F6F8FB; --surface:#FFFFFF; --border:#E8EBF0;
    --text:#14171F; --text-dim:#666B76; --text-faint:#9AA0AB;
    --blue:#0B6FE0; --blue-dark:#0958B5; --blue-soft:#EAF2FE;
    --shadow-sm:0 1px 2px rgba(20,23,31,0.05), 0 1px 1px rgba(20,23,31,0.04);
    --shadow-lg:0 20px 50px rgba(20,23,31,0.12), 0 4px 12px rgba(20,23,31,0.06);
    --radius:16px; --font:'Inter', sans-serif;
  }
  *{margin:0;padding:0;box-sizing:border-box;}
  body{
    background:var(--bg);color:var(--text);font-family:var(--font);-webkit-font-smoothing:antialiased;
    background-image:radial-gradient(circle, rgba(20,23,31,0.12) 1.4px, transparent 1.4px);
    background-size:22px 22px;
    min-height:100vh;
  }
  a{color:var(--blue);text-decoration:none;}
  a:hover{color:var(--blue-dark);}

  .top{display:flex;justify-content:center;padding:32px 0 0;}
  .logo{display:flex;align-items:center;gap:8px;font-weight:800;font-size:18px;letter-spacing:-0.01em;color:var(--text);}
  .logo .sq{width:22px;height:22px;border-radius:6px;background:linear-gradient(135deg,#2D82E8,#0847A0);
    display:flex;align-items:center;justify-content:center;}
  .logo .sq span{width:7px;height:7px;border-radius:2px;background:#fff;}

  .wrap{display:flex;justify-content:center;padding:48px 20px 80px;}
  .doc{
    width:100%;max-width:720px;background:var(--surface);border:1px solid var(--border);
    border-radius:var(--radius);box-shadow:var(--shadow-lg);padding:44px 40px;
  }
  .doc h1{font-size:26px;font-weight:800;letter-spacing:-0.01em;margin-bottom:6px;}
  .doc .updated{font-size:13px;color:var(--text-faint);margin-bottom:32px;}
  .doc h2{font-size:16px;font-weight:800;margin:28px 0 10px;color:var(--text);}
  .doc p{font-size:14.5px;line-height:1.7;color:var(--text-dim);margin-bottom:12px;}
  .doc ul{margin:0 0 12px 20px;}
  .doc li{font-size:14.5px;line-height:1.7;color:var(--text-dim);margin-bottom:6px;}
  .doc h2:first-of-type{margin-top:0;}

  .back{display:inline-flex;align-items:center;gap:6px;font-size:13.5px;color:var(--text-dim);margin-bottom:24px;}
  .back:hover{color:var(--text);}

  @media(max-width:600px){.doc{padding:32px 24px;}}
</style>
</head>
<body>

<div class="top">
  <a href="{{ route('welcome') }}" class="logo"><img src="{{ asset('images/logo2.svg') }}" alt="CreatorSpot" style="width:66px;height:66px;border-radius:6px;object-fit:contain;">CreatorSpot<span style="background:transparent;color:#D6249F;font-size:9px;font-weight:800;letter-spacing:0.06em;padding:2px 11px;border-radius:999px;text-transform:uppercase;border:1px solid #D6249F;margin-left:2px;">Beta</span></a>
</div>

<div class="wrap">
  <div class="doc">
    <a href="{{ route('welcome') }}" class="back">← {{ __('Назад кон почетна') }}</a>

    <h1>{{ __('Политика за приватност') }}</h1>
    <p class="updated">{{ __('Последно ажурирано:') }} 04.08.2026</p>

    <h2>1. {{ __('Кои податоци ги собираме') }}</h2>
    <ul>
      <li>{{ __('Основни податоци: име, е-пошта, лозинка (или Google профил при најава преку Google).') }}</li>
      <li>{{ __('Профилни податоци: телефон, локација (земја/град), профилна слика, био, вештини, портфолио, ценовник.') }}</li>
      <li>{{ __('Содржина што ја креираш: огласи за проекти, апликации, пораки, ревјуа.') }}</li>
      <li>{{ __('Техничи податоци: IP адреса (за автоматско препознавање на земја при регистрација), јазик на прелистувачот.') }}</li>
    </ul>

    <h2>2. {{ __('Како ги користиме податоците') }}</h2>
    <ul>
      <li>{{ __('За да го овозможиме функционирањето на платформата (профили, огласи, апликации, пораки).') }}</li>
      <li>{{ __('За да те поврземе со соодветни клиенти или креативци.') }}</li>
      <li>{{ __('За да испраќаме известувања поврзани со твојата активност (пораки, верификација, статус на апликации).') }}</li>
      <li>{{ __('За да го подобруваме квалитетот и безбедноста на платформата.') }}</li>
    </ul>

    <h2>3. {{ __('Автоматско препознавање на локација') }}</h2>
    <p>{{ __('При регистрација, привремено ја користиме IP адресата за да предложиме земја во формата (преку надворешен сервис за geo-lookup). Ова е само предлог кој секогаш можеш да го промениш рачно; IP адресата не се прикажува јавно и не се чува трајно поврзана со профилот.') }}</p>

    <h2>4. {{ __('Колачиња (cookies)') }}</h2>
    <p>{{ __('Користиме неопходни колачиња за одржување на сесијата (најава) и за паметење на избраниот јазик. Не користиме колачиња за рекламно следење.') }}</p>

    <h2>5. {{ __('Најава преку Google') }}</h2>
    <p>{{ __('Ако се најавиш преку Google, добиваме основни податоци од твојот Google профил (име, е-пошта, профилна слика) со твоја дозвола, преку стандардниот Google најава процес. Не добиваме пристап до твојата Gmail пошта или други Google услуги.') }}</p>

    <h2>6. {{ __('Споделување со трети страни') }}</h2>
    <p>{{ __('Не ги продаваме твоите податоци. Ги споделуваме само со сервиси неопходни за работа на платформата (на пр. давател на е-пошта услуга за испраќање известувања, сервис за geo-lookup при регистрација), секогаш во рамки на потребното за таа услуга.') }}</p>

    <h2>7. {{ __('Чување на податоци') }}</h2>
    <p>{{ __('Ги чуваме твоите податоци додека сметката е активна. Ако ја избришеш сметката, личните податоци се отстрануваат или анонимизираат во разумен рок, освен ако не постои законска обврска за подолго чување.') }}</p>

    <h2>8. {{ __('Твоите права') }}</h2>
    <ul>
      <li>{{ __('Пристап и исправка на твоите податоци директно преку поставките на профилот.') }}</li>
      <li>{{ __('Барање за бришење на сметката и податоците.') }}</li>
      <li>{{ __('Барање копија од податоците што ги имаме за тебе.') }}</li>
    </ul>
    <p>{{ __('За остварување на овие права, контактирај нѐ преку контакт формата.') }}</p>

    <h2>9. {{ __('Безбедност') }}</h2>
    <p>{{ __('Лозинките се чуваат криптирани. Пристапот до податоците е ограничен само на персоналот на CreatorSpot кому му е потребен за работа на платформата.') }}</p>

    <h2>10. {{ __('Измени на политиката') }}</h2>
    <p>{{ __('Можеме да ја ажурираме оваа политика повремено. Значајни измени ќе бидат објавени на оваа страница со нов датум на ажурирање.') }}</p>

    <h2>11. {{ __('Контакт') }}</h2>
    <p>{{ __('За прашања поврзани со приватноста, пиши ни преку') }} <a href="{{ route('contact.create') }}">{{ __('контакт формата') }}</a>.</p>
  </div>
</div>

</body>
</html>
