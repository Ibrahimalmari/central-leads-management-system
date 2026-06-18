<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Form;
use App\Models\Lead;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadInfolistTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_lead_details_with_professional_extra_data_layout(): void
    {
        app()->setLocale('ar');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $company = Company::create(['name' => 'وثيقة']);
        $site = Site::create([
            'company_id' => $company->id,
            'name' => 'watheeqa',
            'url' => 'https://watheeqa.app',
            'site_key' => 'watheeqa',
            'api_key' => 'secret',
            'status' => 'active',
        ]);
        $form = Form::create([
            'site_id' => $site->id,
            'form_key' => 'contact_us',
            'name' => 'contact_us',
            'type' => 'contact',
            'status' => 'active',
        ]);
        $lead = Lead::create([
            'company_id' => $company->id,
            'site_id' => $site->id,
            'form_id' => $form->id,
            'form_key' => 'contact_us',
            'name' => 'محمد العمري',
            'email' => 'lead@example.com',
            'message' => 'نحتاج عرض سعر',
            'status' => 'new',
            'raw_data' => [
                'site_name' => 'وثيقة',
                'page_title' => 'تواصل معنا',
                'request_type' => 'حجز عرض',
            ],
        ]);

        $this
            ->actingAs($admin)
            ->get("/admin/leads/{$lead->id}")
            ->assertOk()
            ->assertSee('ملخص الطلب')
            ->assertSee('البيانات الإضافية')
            ->assertSee('اسم الموقع')
            ->assertSee('حجز عرض');
    }
}
