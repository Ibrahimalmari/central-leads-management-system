<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocalizationTest extends TestCase
{
    public function test_admin_login_uses_english_locale_from_session(): void
    {
        $this
            ->withSession(['locale' => 'en'])
            ->get('/admin/login')
            ->assertOk()
            ->assertSee('dir="ltr"', false)
            ->assertSee('Welcome to the leads dashboard')
            ->assertSee('/language/ar', false);
    }

    public function test_admin_login_uses_arabic_locale_from_session(): void
    {
        $this
            ->withSession(['locale' => 'ar'])
            ->get('/admin/login')
            ->assertOk()
            ->assertSee('dir="rtl"', false)
            ->assertSee('/language/en', false);
    }

    public function test_language_switch_is_persisted_in_cookie_for_logged_out_pages(): void
    {
        $this
            ->get('/language/ar')
            ->assertRedirect()
            ->assertCookie('locale', 'ar');

        $this
            ->withCookie('locale', 'ar')
            ->get('/admin/login')
            ->assertOk()
            ->assertSee('dir="rtl"', false)
            ->assertSee('/language/en', false);
    }

    public function test_integration_test_form_is_available(): void
    {
        $this
            ->get('/integration-test/form')
            ->assertOk()
            ->assertSee('/api/leads');
    }
}
