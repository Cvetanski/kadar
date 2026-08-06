<nav>
  <div class="nav-inner">
    <a href="{{ route('welcome') }}" class="logo"><span class="sq"><span></span></span>CreatorSpot<span style="background:transparent;color:#D6249F;font-size:9px;font-weight:800;letter-spacing:0.06em;padding:2px 11px;border-radius:999px;text-transform:uppercase;border:1px solid #D6249F;">Beta</span></a>
    <div class="nav-links">
      <a href="#{{ \App\Support\AnchorSlugs::for('kategorii') }}">{{ __('Категории') }}</a>
      <a href="#{{ \App\Support\AnchorSlugs::for('kreativci') }}">{{ __('Креативци') }}</a>
      <a href="#{{ \App\Support\AnchorSlugs::for('kako') }}">{{ __('Како функционира') }}</a>
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
    <a href="#{{ \App\Support\AnchorSlugs::for('kategorii') }}" onclick="closeMobileNav()">{{ __('Категории') }}</a>
    <a href="#{{ \App\Support\AnchorSlugs::for('kreativci') }}" onclick="closeMobileNav()">{{ __('Креативци') }}</a>
    <a href="#{{ \App\Support\AnchorSlugs::for('kako') }}" onclick="closeMobileNav()">{{ __('Како функционира') }}</a>
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
