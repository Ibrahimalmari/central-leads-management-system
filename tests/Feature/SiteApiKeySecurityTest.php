<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteApiKeySecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_site_api_key_is_stored_as_hash_with_preview_only(): void
    {
        $company = Company::create(['name' => 'Acme']);

        $site = Site::create([
            'company_id' => $company->id,
            'name' => 'Acme Website',
            'url' => 'https://example.com',
            'site_key' => 'acme',
            'api_key' => 'sk_secret_key_for_tests',
            'status' => 'active',
        ]);

        $this->assertSame(Site::hashApiKey('sk_secret_key_for_tests'), $site->getRawOriginal('api_key'));
        $this->assertSame('sk_secre...ests', $site->api_key_preview);
        $this->assertDatabaseMissing('sites', [
            'api_key' => 'sk_secret_key_for_tests',
        ]);
    }
}
