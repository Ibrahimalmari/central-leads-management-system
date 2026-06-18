<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Form;
use App\Models\Lead;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_only_sees_leads_from_their_company(): void
    {
        [$ownLead, $otherLead, $manager] = $this->makeScopedLeadScenario();

        $this
            ->actingAs($manager)
            ->get('/admin/leads')
            ->assertOk()
            ->assertSee($ownLead->email)
            ->assertDontSee($otherLead->email);

        $this
            ->actingAs($manager)
            ->get("/admin/leads/{$ownLead->id}")
            ->assertOk();

        $response = $this
            ->actingAs($manager)
            ->get("/admin/leads/{$otherLead->id}");

        $this->assertContains($response->getStatusCode(), [403, 404]);
    }

    public function test_agent_only_sees_assigned_leads(): void
    {
        [$ownLead, $otherLead, , $agent] = $this->makeScopedLeadScenario();

        $ownLead->update(['assigned_to' => $agent->id]);

        $this
            ->actingAs($agent)
            ->get('/admin/leads')
            ->assertOk()
            ->assertSee($ownLead->email)
            ->assertDontSee($otherLead->email);

        $this
            ->actingAs($agent)
            ->get("/admin/leads/{$ownLead->id}")
            ->assertOk();

        $response = $this
            ->actingAs($agent)
            ->get("/admin/leads/{$otherLead->id}");

        $this->assertContains($response->getStatusCode(), [403, 404]);
    }

    public function test_agent_cannot_access_company_site_or_form_resources(): void
    {
        [$ownLead, , , $agent] = $this->makeScopedLeadScenario();

        foreach ([
            "/admin/companies/{$ownLead->company_id}",
            "/admin/sites/{$ownLead->site_id}",
            "/admin/forms/{$ownLead->form_id}",
        ] as $url) {
            $response = $this->actingAs($agent)->get($url);

            $this->assertContains($response->getStatusCode(), [403, 404]);
        }
    }

    private function makeScopedLeadScenario(): array
    {
        $ownCompany = Company::create(['name' => 'Own Company']);
        $otherCompany = Company::create(['name' => 'Other Company']);

        $ownSite = $this->makeSite($ownCompany, 'own');
        $otherSite = $this->makeSite($otherCompany, 'other');

        $ownForm = $this->makeForm($ownSite, 'own_form');
        $otherForm = $this->makeForm($otherSite, 'other_form');

        $manager = User::factory()->create([
            'role' => 'manager',
            'company_id' => $ownCompany->id,
        ]);

        $agent = User::factory()->create([
            'role' => 'agent',
            'company_id' => $ownCompany->id,
        ]);

        $ownLead = Lead::create([
            'company_id' => $ownCompany->id,
            'site_id' => $ownSite->id,
            'form_id' => $ownForm->id,
            'form_key' => 'own_form',
            'name' => 'Own Lead',
            'email' => 'own-lead@example.com',
            'status' => 'new',
        ]);

        $otherLead = Lead::create([
            'company_id' => $otherCompany->id,
            'site_id' => $otherSite->id,
            'form_id' => $otherForm->id,
            'form_key' => 'other_form',
            'name' => 'Other Lead',
            'email' => 'other-lead@example.com',
            'status' => 'new',
        ]);

        return [$ownLead, $otherLead, $manager, $agent];
    }

    private function makeSite(Company $company, string $key): Site
    {
        return Site::create([
            'company_id' => $company->id,
            'name' => "{$key} site",
            'url' => "https://{$key}.example.com",
            'site_key' => $key,
            'api_key' => "sk_{$key}",
            'status' => 'active',
        ]);
    }

    private function makeForm(Site $site, string $key): Form
    {
        return Form::create([
            'site_id' => $site->id,
            'form_key' => $key,
            'name' => $key,
            'type' => 'contact',
            'status' => 'active',
        ]);
    }
}
