<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Form;
use App\Models\Lead;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_lead_with_a_valid_bearer_api_key(): void
    {
        $company = Company::create(['name' => 'Acme']);
        $site = Site::create([
            'company_id' => $company->id,
            'name' => 'Acme Website',
            'url' => 'https://example.com',
            'site_key' => 'acme',
            'api_key' => 'sk_test',
            'status' => 'active',
        ]);
        $form = Form::create([
            'site_id' => $site->id,
            'form_key' => 'contact_main',
            'name' => 'Contact Form',
            'type' => 'contact',
            'status' => 'active',
        ]);

        $response = $this
            ->withHeader('Authorization', 'Bearer sk_test')
            ->postJson('/api/leads', [
                'form_key' => 'contact_main',
                'name' => 'Ahmed',
                'email' => 'ahmed@example.com',
                'phone' => '+966500000000',
                'message' => 'Need a quote',
                'page_url' => 'https://example.com/contact',
                'raw_data' => ['budget' => '5000 SAR'],
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('leads', [
            'company_id' => $company->id,
            'site_id' => $site->id,
            'form_id' => $form->id,
            'email' => 'ahmed@example.com',
            'status' => 'new',
        ]);
    }

    public function test_it_rejects_an_invalid_api_key(): void
    {
        $response = $this
            ->withHeader('X-API-Key', 'bad_key')
            ->postJson('/api/leads', ['name' => 'Ahmed']);

        $response
            ->assertUnauthorized()
            ->assertJsonPath('success', false);

        $this->assertSame(0, Lead::count());
    }

    public function test_it_rejects_an_inactive_site(): void
    {
        $company = Company::create(['name' => 'Acme']);
        Site::create([
            'company_id' => $company->id,
            'name' => 'Acme Website',
            'url' => 'https://example.com',
            'site_key' => 'acme',
            'api_key' => 'sk_test',
            'status' => 'inactive',
        ]);

        $response = $this
            ->withHeader('Authorization', 'Bearer sk_test')
            ->postJson('/api/leads', ['name' => 'Ahmed']);

        $response
            ->assertForbidden()
            ->assertJsonPath('success', false);

        $this->assertSame(0, Lead::count());
    }

    public function test_it_returns_a_consistent_validation_error_response(): void
    {
        $company = Company::create(['name' => 'Acme']);
        Site::create([
            'company_id' => $company->id,
            'name' => 'Acme Website',
            'url' => 'https://example.com',
            'site_key' => 'acme',
            'api_key' => 'sk_test',
            'status' => 'active',
        ]);

        $response = $this
            ->withHeader('Authorization', 'Bearer sk_test')
            ->postJson('/api/leads', [
                'email' => 'not-an-email',
                'page_url' => 'not-a-url',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => [
                    'email',
                    'page_url',
                ],
            ]);

        $this->assertSame(0, Lead::count());
    }
}
