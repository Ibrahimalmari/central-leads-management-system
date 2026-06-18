<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Form;
use App\Models\Lead;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['name' => 'Demo Company'],
            [
                'email' => 'info@example.com',
                'phone' => '+966500000000',
                'status' => 'active',
            ],
        );

        $site = Site::firstOrCreate(
            ['site_key' => 'demo_site'],
            [
                'company_id' => $company->id,
                'name' => 'Demo Website',
                'url' => 'https://example.com',
                'api_key' => 'sk_demo_123456789',
                'status' => 'active',
            ],
        );

        $contactForm = Form::firstOrCreate(
            [
                'site_id' => $site->id,
                'form_key' => 'contact_main',
            ],
            [
                'name' => 'Main Contact Form',
                'type' => 'contact',
                'status' => 'active',
            ],
        );

        Form::firstOrCreate(
            [
                'site_id' => $site->id,
                'form_key' => 'quote_home',
            ],
            [
                'name' => 'Home Quote Form',
                'type' => 'quote',
                'status' => 'active',
            ],
        );

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
        );

        Lead::firstOrCreate(
            [
                'site_id' => $site->id,
                'email' => 'ahmed@example.com',
            ],
            [
                'company_id' => $company->id,
                'form_id' => $contactForm->id,
                'form_key' => $contactForm->form_key,
                'form_name' => $contactForm->name,
                'form_type' => $contactForm->type,
                'name' => 'Ahmed',
                'phone' => '+966500000000',
                'message' => 'I need a quote for a company website.',
                'page_url' => 'https://example.com/services',
                'status' => 'new',
                'raw_data' => [
                    'service' => 'Website Design',
                    'budget' => '5000 SAR',
                ],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Seeder',
            ],
        );
    }
}
