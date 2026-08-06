<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ __('Услови за користење') }} — CreatorSpot</title>
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
  <a href="{{ route('welcome') }}" class="logo"><span class="sq"><span></span></span>CreatorSpot<span style="background:transparent;color:#D6249F;font-size:9px;font-weight:800;letter-spacing:0.06em;padding:2px 11px;border-radius:999px;text-transform:uppercase;border:1px solid #D6249F;margin-left:2px;">Beta</span></a>
</div>

<div class="wrap">
  <div class="doc">
    <a href="{{ route('welcome') }}" class="back">← {{ __('Назад кон почетна') }}</a>

    <h1>{{ __('Услови за користење') }}</h1>
    <p class="updated">{{ __('Последно ажурирано:') }} 04.08.2026</p>

    <h2>1. {{ __('Прифаќање на условите') }}</h2>
    <p>{{ __('Со креирање профил или користење на CreatorSpot (платформата), се согласуваш со овие Услови за користење. Ако не се согласуваш, немој да ја користиш платформата.') }}</p>

    <h2>2. {{ __('Што е CreatorSpot') }}</h2>
    <p>{{ __('CreatorSpot е онлајн платформа што поврзува клиенти со независни креативци (видеографи, фотографи, дизајнери, дигитални маркетери и едитори) низ Балканот. Платформата ги олеснува пронаоѓањето, договарањето и комуникацијата меѓу страните, но не е страна во договорот што клиентот и креативецот го склучуваат меѓу себе.') }}</p>

    <h2>3. {{ __('Регистрација и сметка') }}</h2>
    <p>{{ __('При регистрација си должен да дадеш точни и целосни податоци. Одговорен си за чувањето на пристапот до твојата сметка и за сите активности што се случуваат преку неа. Регистрацијата преку Google подлежи и на условите на Google.') }}</p>

    <h2>4. {{ __('Обврски на клиентите') }}</h2>
    <ul>
      <li>{{ __('Да ги опишеш проектите точно и целосно.') }}</li>
      <li>{{ __('Да комуницираш чесно и навремено со креативците.') }}</li>
      <li>{{ __('Да ги почитуваш договорените услови (буџет, рок, испорака) со креативецот.') }}</li>
    </ul>

    <h2>5. {{ __('Обврски на креативците') }}</h2>
    <ul>
      <li>{{ __('Да прикажуваш точно портфолио и вештини во профилот.') }}</li>
      <li>{{ __('Да ги испорачуваш договорените услуги во договорениот рок и квалитет.') }}</li>
      <li>{{ __('Да не објавуваш содржина што ги повредува правата на трети лица.') }}</li>
    </ul>

    <h2>6. {{ __('Плаќања и договори') }}</h2>
    <p>{{ __('Условите за плаќање (цена, динамика, начин на исплата) ги договараат клиентот и креативецот директно меѓу себе. Платформата моментално не обработува плаќања во име на корисниците. Секоја страна презема сопствен ризик при склучување договор надвор од платформата.') }}</p>

    <h2>7. {{ __('Верификација на профили') }}</h2>
    <p>{{ __('Ознаката „Верифициран" значи дека администраторски тим рачно го прегледал профилот (завршен onboarding и поставена профилна слика). Верификацијата е сигнал на доверба, не гаранција за квалитетот на извршената работа.') }}</p>

    <h2>8. {{ __('Забранета употреба') }}</h2>
    <p>{{ __('Забрането е користење на платформата за измама, вознемирување, објавување невистинити информации, заобиколување на платформата за да се избегнат нејзините правила, или каква било активност спротивна на законот.') }}</p>

    <h2>9. {{ __('Ограничување на одговорност') }}</h2>
    <p>{{ __('CreatorSpot ја обезбедува платформата „како што е" и не гарантира непрекинат или безгрешен пристап. Платформата не одговара за квалитетот на извршената работа, ниту за спорови меѓу клиенти и креативци настанати надвор од директната контрола на платформата.') }}</p>

    <h2>10. {{ __('Прекин на сметка') }}</h2>
    <p>{{ __('Задржуваме право да суспендираме или избришеме сметка што ги прекршува овие услови, без претходна најава, доколку е потребно за заштита на другите корисници или платформата.') }}</p>

    <h2>11. {{ __('Измени на условите') }}</h2>
    <p>{{ __('Можеме да ги ажурираме овие услови повремено. Продолженото користење на платформата по објавена измена значи дека ги прифаќаш новите услови.') }}</p>

    <h2>12. {{ __('Контакт') }}</h2>
    <p>{{ __('За прашања поврзани со овие услови, пиши ни преку') }} <a href="{{ route('contact.create') }}">{{ __('контакт формата') }}</a>.</p>
  </div>
</div>

</body>
</html>
