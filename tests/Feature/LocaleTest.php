<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_defaults_to_mk_with_no_cookie_or_accept_language(): void
    {
        $response = $this->get('/login');

        $response->assertSee('lang="mk"', false);
    }

    public function test_detects_locale_from_accept_language_header(): void
    {
        $response = $this->withHeaders(['Accept-Language' => 'sr-RS,sr;q=0.9,en;q=0.8'])->get('/login');

        $response->assertSee('lang="sr"', false);
    }

    public function test_accept_language_weights_are_respected(): void
    {
        // "fr" is listed first but has a lower quality value than "hr", and
        // "fr" isn't supported anyway — "hr" should win.
        $response = $this->withHeaders(['Accept-Language' => 'fr;q=0.5,hr;q=0.9'])->get('/login');

        $response->assertSee('lang="hr"', false);
    }

    public function test_detects_english_from_accept_language_header(): void
    {
        $response = $this->withHeaders(['Accept-Language' => 'en-US,en;q=0.9'])->get('/login');

        $response->assertSee('lang="en"', false);
    }

    public function test_unsupported_accept_language_falls_back_to_mk(): void
    {
        $response = $this->withHeaders(['Accept-Language' => 'fr-FR,fr;q=0.9,de;q=0.8'])->get('/login');

        $response->assertSee('lang="mk"', false);
    }

    public function test_locale_cookie_takes_priority_over_accept_language(): void
    {
        $response = $this->withHeaders(['Accept-Language' => 'sr'])
            ->withCookie('locale', 'bg')
            ->get('/login');

        $response->assertSee('lang="bg"', false);
    }

    public function test_authenticated_users_saved_locale_takes_priority_over_cookie(): void
    {
        $user = User::factory()->create(['role' => 'client', 'locale' => 'el']);

        $response = $this->actingAs($user)
            ->withCookie('locale', 'bg')
            ->get('/dashboard');

        $response->assertSee('lang="el"', false);
    }

    public function test_switching_locale_sets_a_long_lived_cookie(): void
    {
        $response = $this->post('/locale', ['option' => 'sq_al']);

        $response->assertCookie('locale', 'sq');
        $response->assertCookie('locale_option', 'sq_al');
    }

    public function test_switching_locale_while_authenticated_persists_to_the_user_record(): void
    {
        $user = User::factory()->create(['role' => 'client', 'locale' => 'mk']);

        $this->actingAs($user)->post('/locale', ['option' => 'el']);

        $this->assertSame('el', $user->fresh()->locale);
    }

    public function test_switching_to_an_invalid_option_fails_validation(): void
    {
        $response = $this->post('/locale', ['option' => 'not-a-real-option']);

        $response->assertSessionHasErrors('option');
    }

    public function test_both_albanian_switcher_options_resolve_to_the_sq_locale(): void
    {
        $response = $this->post('/locale', ['option' => 'sq_xk']);

        $response->assertCookie('locale', 'sq');
        $response->assertCookie('locale_option', 'sq_xk');
    }

    public function test_switching_to_english_works(): void
    {
        $response = $this->post('/locale', ['option' => 'en']);

        $response->assertCookie('locale', 'en');

        $follow = $this->withCookie('locale', 'en')->get('/login');
        $follow->assertSee('lang="en"', false);
    }
}
