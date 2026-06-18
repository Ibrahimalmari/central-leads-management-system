<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Form;
use App\Models\Lead;
use App\Models\Site;
use App\Support\LeadCsvExport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadCsvExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_exports_all_rows_and_dynamic_raw_data_columns(): void
    {
        app()->setLocale('en');

        $company = Company::create(['name' => 'Watheeqa']);
        $site = Site::create([
            'company_id' => $company->id,
            'name' => 'Main site',
            'url' => 'https://example.com',
            'site_key' => 'main',
            'api_key' => 'secret',
            'status' => 'active',
        ]);
        $form = Form::create([
            'site_id' => $site->id,
            'form_key' => 'contact_us',
            'name' => 'Contact us',
            'type' => 'contact',
            'status' => 'active',
        ]);

        $first = Lead::create([
            'company_id' => $company->id,
            'site_id' => $site->id,
            'form_id' => $form->id,
            'form_key' => 'contact_us',
            'name' => 'First Lead',
            'email' => 'first@example.com',
            'status' => 'new',
            'raw_data' => [
                'اسم الموقع' => 'وثيقة',
                'نوع الطلب' => 'حجز عرض',
            ],
        ]);

        $second = Lead::create([
            'company_id' => $company->id,
            'site_id' => $site->id,
            'form_id' => $form->id,
            'form_key' => 'contact_us',
            'name' => 'Second Lead',
            'email' => 'second@example.com',
            'status' => 'new',
            'raw_data' => [
                'sector' => 'contracting',
                'budget' => '5000',
            ],
        ]);

        $response = app(LeadCsvExport::class)->downloadFromQuery(
            Lead::query()->orderBy('id'),
            'test.csv',
        );

        ob_start();
        $response->sendContent();
        $csv = (string) ob_get_clean();

        $lines = preg_split('/\r\n|\n|\r/', trim($csv));
        $headers = str_getcsv(ltrim($lines[0] ?? '', "\xEF\xBB\xBF"));

        $this->assertCount(3, $lines);
        $this->assertContains('اسم الموقع', $headers);
        $this->assertContains('نوع الطلب', $headers);
        $this->assertContains('Sector', $headers);
        $this->assertContains('Budget', $headers);
        $this->assertStringContainsString((string) $first->id, $csv);
        $this->assertStringContainsString((string) $second->id, $csv);
    }
}
