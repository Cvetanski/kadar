<nav>
  <div class="nav-inner">
    <a href="{{ route('welcome') }}" class="logo"><span class="sq"><span></span></span>KADAR<span style="background:transparent;color:#D6249F;font-size:9px;font-weight:800;letter-spacing:0.06em;padding:2px 11px;border-radius:999px;text-transform:uppercase;border:1px solid #D6249F;">Beta</span></a>
    <div class="nav-links">
      <a href="#kategorii">{{ __('Категории') }}</a>
      <a href="#kreativci">{{ __('Креативци') }}</a>
      <a href="#kako">{{ __('Како функционира') }}</a>
      <a href="#zanas">{{ __('За нас') }}</a>
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
  </div>
</nav>
