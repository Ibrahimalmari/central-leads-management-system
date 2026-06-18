<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemSettingAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_system_settings(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this
            ->actingAs($admin)
            ->get('/admin/system-settings')
            ->assertOk()
            ->assertSee('إعدادات النظام');
    }

    public function test_admin_can_access_mail_test_action(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this
            ->withSession(['locale' => 'en'])
            ->actingAs($admin)
            ->get('/admin/system-settings/1/edit')
            ->assertOk()
            ->assertSee('Send test email');
    }

    public function test_non_admin_cannot_access_system_settings(): void
    {
        $agent = User::factory()->create([
            'role' => 'agent',
        ]);

        $this
            ->actingAs($agent)
            ->get('/admin/system-settings')
            ->assertForbidden();
    }
}
