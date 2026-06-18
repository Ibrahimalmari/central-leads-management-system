<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrationGuideTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_view_integration_guide_with_their_site_key(): void
    {
        $company = Company::create(['name' => 'Watheeqa']);
        $site = Site::create([
            'company_id' => $company->id,
            'name' => 'Main site',
            'url' => 'https://watheeqa.app',
            'site_key' => 'watheeqa',
            'api_key' => 'sk_manager_test',
            'status' => 'active',
        ]);
        $manager = User::factory()->create([
            'role' => 'manager',
            'company_id' => $company->id,
        ]);

        $this
            ->actingAs($manager)
            ->get('/admin/integration-guide')
            ->assertOk()
            ->assertSee('/api/leads')
            ->assertSee($site->api_key_preview)
            ->assertDontSee('sk_manager_test');
    }

    public function test_agent_cannot_view_integration_guide(): void
    {
        $agent = User::factory()->create([
            'role' => 'agent',
        ]);

        $response = $this
            ->actingAs($agent)
            ->get('/admin/integration-guide');

        $this->assertContains($response->getStatusCode(), [403, 404]);
    }
}
