<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Form;
use App\Models\Lead;
use App\Models\Site;
use App\Models\User;
use App\Support\DashboardLeadFilters;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardLeadFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_filters_apply_status_site_date_and_access_scope(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$site, $form] = $this->makeSiteWithForm('Main');
        [$otherSite, $otherForm] = $this->makeSiteWithForm('Other');

        $matchingLead = $this->makeLead($site, $form, [
            'status' => 'won',
            'created_at' => now()->subDays(2),
        ]);

        $this->makeLead($site, $form, [
            'status' => 'new',
            'created_at' => now()->subDays(2),
        ]);

        $this->makeLead($otherSite, $otherForm, [
            'status' => 'won',
            'created_at' => now()->subDays(10),
        ]);

        $this->actingAs($admin);

        $ids = DashboardLeadFilters::apply(Lead::query(), [
            'from' => now()->subDays(3)->toDateString(),
            'until' => now()->toDateString(),
            'site_id' => $site->id,
            'status' => 'won',
        ])->pluck('id')->all();

        $this->assertSame([$matchingLead->id], $ids);
    }

    public function test_dashboard_page_is_available_to_authenticated_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this
            ->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee(__('admin.stats.dashboard_filters'));
    }

    /**
     * @return array{0: Site, 1: Form}
     */
    private function makeSiteWithForm(string $name): array
    {
        $company = Company::create(['name' => "{$name} Company"]);

        $site = Site::create([
            'company_id' => $company->id,
            'name' => "{$name} Site",
            'url' => "https://{$name}.example.com",
            'site_key' => strtolower($name),
            'api_key' => "sk_{$name}",
            'status' => 'active',
        ]);

        $form = Form::create([
            'site_id' => $site->id,
            'form_key' => strtolower($name).'_form',
            'name' => "{$name} Form",
            'type' => 'contact',
            'status' => 'active',
        ]);

        return [$site, $form];
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeLead(Site $site, Form $form, array $attributes): Lead
    {
        $lead = Lead::create([
            'company_id' => $site->company_id,
            'site_id' => $site->id,
            'form_id' => $form->id,
            'form_key' => $form->form_key,
            'name' => $attributes['name'] ?? 'Lead',
            'email' => $attributes['email'] ?? fake()->safeEmail(),
            'status' => $attributes['status'] ?? 'new',
        ]);

        $lead->forceFill([
            'created_at' => $attributes['created_at'] ?? now(),
            'updated_at' => $attributes['updated_at'] ?? now(),
        ])->save();

        return $lead;
    }
}
