<style>
  :root{
    --pn-bg:#FFFFFF; --pn-border:#E8EBF0; --pn-text:#14171F; --pn-text-dim:#666B76; --pn-text-faint:#9AA0AB;
    --pn-blue:#0B6FE0; --pn-blue-dark:#0958B5;
    --pn-shadow-sm:0 1px 2px rgba(20,23,31,0.05), 0 1px 1px rgba(20,23,31,0.04);
    --pn-shadow-md:0 8px 24px rgba(20,23,31,0.08), 0 2px 6px rgba(20,23,31,0.04);
    --pn-font:'Inter', sans-serif;
  }
  nav{position:sticky;top:0;z-index:50;background:rgba(255,255,255,0.9);
    backdrop-filter:blur(10px);border-bottom:1px solid var(--pn-border);font-family:var(--pn-font);}
  nav a{text-decoration:none;color:inherit;}
  .nav-inner{max-width:1160px;margin:0 auto;padding:16px 32px;
    display:flex;align-items:center;justify-content:space-between;}
  .logo{display:flex;align-items:center;gap:8px;font-weight:800;font-size:18px;letter-spacing:-0.01em;color:var(--pn-text);}
  .logo .sq{width:22px;height:22px;border-radius:6px;background:linear-gradient(135deg, #2D82E8, #0847A0);
    display:flex;align-items:center;justify-content:center;}
  .logo .sq span{width:7px;height:7px;border-radius:2px;background:#fff;}
  .nav-links{display:flex;gap:32px;font-size:14.5px;color:var(--pn-text-dim);font-weight:500;}
  .nav-links a:hover{color:var(--pn-text);}
  .nav-cta{display:flex;gap:12px;align-items:center;}
  @media(max-width:900px){.nav-links{display:none;}}

  .nav-burger{display:none;flex-direction:column;justify-content:center;gap:5px;
    background:none;border:none;cursor:pointer;padding:8px;}
  .nav-burger span{width:22px;height:2px;background:var(--pn-text);border-radius:2px;display:block;}

  .nav-mobile-panel{display:none;flex-direction:column;padding:8px 20px 20px;
    border-top:1px solid var(--pn-border);background:#fff;}
  .nav-mobile-panel.is-open{display:flex;}
  .nav-mobile-panel > a{padding:13px 4px;font-size:14.5px;color:var(--pn-text-dim);font-weight:500;
    border-bottom:1px solid var(--pn-border);}
  .nav-mobile-panel > a:hover{color:var(--pn-text);}
  .nav-mobile-actions{display:flex;flex-direction:column;gap:10px;margin-top:14px;}
  .nav-mobile-actions .btn{width:100%;justify-content:center;}
  .nav-mobile-actions .btn-ghost{
    border:1.5px solid #0B6FE0;color:#14171F;
    box-shadow:0 0 0 3px rgba(11,111,224,.14), 0 2px 10px rgba(11,111,224,.3);
  }
  .nav-mobile-lang{margin-top:14px;}

  @media(max-width:640px){
    .nav-inner{padding:14px 20px;}
    .nav-cta{display:none;}
    .nav-burger{display:flex;}
  }

  .nav-inner .btn{display:inline-flex;align-items:center;gap:8px;font-family:var(--pn-font);
    font-weight:600;font-size:14px;padding:10px 18px;border-radius:10px;
    border:1px solid transparent;cursor:pointer;transition:all .15s ease;}
  .nav-mobile-panel .btn{display:inline-flex;align-items:center;gap:8px;font-family:var(--pn-font);
    font-weight:600;font-size:14px;padding:10px 18px;border-radius:10px;
    border:1px solid transparent;cursor:pointer;transition:all .15s ease;}
  .btn-primary{background:linear-gradient(135deg, #2D82E8, #0958B5);color:#fff;box-shadow:var(--pn-shadow-sm);border:none;background-clip:padding-box;}
  .btn-primary:hover{background:linear-gradient(135deg, #1F72D8, #0847A0);transform:translateY(-1px);box-shadow:var(--pn-shadow-md);}
  .btn-ghost{color:var(--pn-text);border-color:var(--pn-border);background:#fff;}
  .btn-ghost:hover{border-color:var(--pn-text-faint);}
</style>

<nav>
  <div class="nav-inner">
    <a href="{{ route('welcome') }}" class="logo"><img src="{{ asset('images/logo2.svg') }}" alt="CreatorSpot" style="width:66px;height:66px;border-radius:6px;object-fit:contain;">CreatorSpot<span style="background:transparent;color:#D6249F;font-size:9px;font-weight:800;letter-spacing:0.06em;padding:2px 11px;border-radius:999px;text-transform:uppercase;border:1px solid #D6249F;">Beta</span></a>
    <div class="nav-links">
      <a href="{{ route('welcome') }}#{{ \App\Support\AnchorSlugs::for('kategorii') }}">{{ __('Категории') }}</a>
      <a href="{{ route('welcome') }}#{{ \App\Support\AnchorSlugs::for('kreativci') }}">{{ __('Креативци') }}</a>
      <a href="{{ route('welcome') }}#{{ \App\Support\AnchorSlugs::for('kako') }}">{{ __('Како функционира') }}</a>
      {{-- "За нас" е привремено исклучено заедно со секцијата на landing page-от. --}}
    </div>
    <div class="nav-cta">
      @auth
        <a href="{{ route('dashboard') }}" class="btn btn-primary">{{ __('Оди на контролна табла') }}</a>
      @else
        <a href="{{ route('login') }}" class="btn btn-ghost">{{ __('Најави се') }}</a>
        <a href="{{ route('register') }}" class="btn btn-primary">{{ __('Регистрирај се бесплатно') }}</a>
      @endauth

      <x-language-switcher short />
    </div>
    <button type="button" class="nav-burger" id="navBurgerBtn" onclick="toggleMobileNav()" aria-label="{{ __('Мени') }}" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>

  <div class="nav-mobile-panel" id="navMobilePanel">
    <a href="{{ route('welcome') }}#{{ \App\Support\AnchorSlugs::for('kategorii') }}" onclick="closeMobileNav()">{{ __('Категории') }}</a>
    <a href="{{ route('welcome') }}#{{ \App\Support\AnchorSlugs::for('kreativci') }}" onclick="closeMobileNav()">{{ __('Креативци') }}</a>
    <a href="{{ route('welcome') }}#{{ \App\Support\AnchorSlugs::for('kako') }}" onclick="closeMobileNav()">{{ __('Како функционира') }}</a>
    <div class="nav-mobile-actions">
      @auth
        <a href="{{ route('dashboard') }}" class="btn btn-primary">{{ __('Оди на контролна табла') }}</a>
      @else
        <a href="{{ route('login') }}" class="btn btn-ghost">{{ __('Најави се') }}</a>
        <a href="{{ route('register') }}" class="btn btn-primary">{{ __('Регистрирај се бесплатно') }}</a>
      @endauth
    </div>
    <div class="nav-mobile-lang">
      <x-language-switcher short />
    </div>
  </div>
</nav>

<script>
  function toggleMobileNav(){
    var panel = document.getElementById('navMobilePanel');
    var burger = document.getElementById('navBurgerBtn');
    var isOpen = panel.classList.toggle('is-open');
    burger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  }
  function closeMobileNav(){
    document.getElementById('navMobilePanel').classList.remove('is-open');
    document.getElementById('navBurgerBtn').setAttribute('aria-expanded', 'false');
  }
</script>
