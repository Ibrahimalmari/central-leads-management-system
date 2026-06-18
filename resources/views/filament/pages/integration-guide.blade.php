<x-filament-panels::page>
    @php
        $endpoint = $this->getEndpoint();
        $sites = $this->getSites();
        $sampleKey = 'SITE_API_KEY';
        $payload = [
            'form_key' => 'contact_us',
            'form_name' => 'Contact form',
            'form_type' => 'contact',
            'name' => 'Customer name',
            'email' => 'customer@example.com',
            'phone' => '+966500000000',
            'message' => 'Message from the website form',
            'page_url' => 'https://example.com/contact',
            'raw_data' => [
                'page_title' => 'Contact us',
                'request_type' => 'Quote request',
                'source' => 'website',
            ],
        ];
        $payloadJson = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $phpExample = <<<'PHP'
<?php

$endpoint = 'ENDPOINT_URL';
$apiKey = 'SITE_API_KEY';

$payload = [
    'form_key' => 'contact_us',
    'form_name' => 'Contact form',
    'form_type' => 'contact',
    'name' => $_POST['name'] ?? null,
    'email' => $_POST['email'] ?? null,
    'phone' => $_POST['phone'] ?? null,
    'message' => $_POST['message'] ?? null,
    'page_url' => 'https://example.com/contact',
    'raw_data' => [
        'page_title' => 'Contact us',
        'request_type' => $_POST['request_type'] ?? null,
        'source' => 'website',
    ],
];

$ch = curl_init($endpoint);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
        'Accept: application/json',
    ],
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
]);

$response = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($response === false || $status >= 400) {
    error_log('Central leads API failed: ' . ($error ?: $response));
}
PHP;
        $phpExample = str_replace(['ENDPOINT_URL', 'SITE_API_KEY'], [$endpoint, $sampleKey], $phpExample);
    @endphp

    <div class="cl-docs" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <section class="cl-docs__hero">
            <div>
                <p>{{ __('admin.integration.kicker') }}</p>
                <h2>{{ __('admin.integration.title') }}</h2>
                <span>{{ __('admin.integration.subtitle') }}</span>
            </div>
            <code>{{ $endpoint }}</code>
        </section>

        <section class="cl-docs__grid">
            <article>
                <h3>{{ __('admin.integration.steps_title') }}</h3>
                <ol>
                    <li>{{ __('admin.integration.step_company') }}</li>
                    <li>{{ __('admin.integration.step_site') }}</li>
                    <li>{{ __('admin.integration.step_form') }}</li>
                    <li>{{ __('admin.integration.step_backend') }}</li>
                    <li>{{ __('admin.integration.step_test') }}</li>
                </ol>
            </article>

            <article>
                <h3>{{ __('admin.integration.security_title') }}</h3>
                <ul>
                    <li>{{ __('admin.integration.security_server') }}</li>
                    <li>{{ __('admin.integration.security_key') }}</li>
                    <li>{{ __('admin.integration.security_timeout') }}</li>
                    <li>{{ __('admin.integration.security_logs') }}</li>
                </ul>
            </article>
        </section>

        <section class="cl-docs__panel">
            <h3>{{ __('admin.integration.sites_title') }}</h3>
            @if ($sites->isEmpty())
                <p class="cl-docs__muted">{{ __('admin.integration.no_sites') }}</p>
            @else
                <div class="cl-docs__sites">
                    @foreach ($sites as $site)
                        <div class="cl-docs__site">
                            <div class="cl-docs__site-head">
                                <strong>{{ $site->name }}</strong>
                                <span>{{ $site->company?->name }}</span>
                            </div>

                            <dl>
                                <div>
                                    <dt>{{ __('admin.integration.site_url') }}</dt>
                                    <dd><code>{{ $site->url }}</code></dd>
                                </div>
                                <div>
                                    <dt>{{ __('admin.integration.api_key') }}</dt>
                                    <dd>
                                        <code>{{ $site->api_key_preview ?: '********' }}</code>
                                        <span>{{ __('admin.integration.api_key_hidden') }}</span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="cl-docs__grid">
            <article>
                <h3>{{ __('admin.integration.payload_title') }}</h3>
                <pre><code>{{ $payloadJson }}</code></pre>
            </article>

            <article>
                <h3>{{ __('admin.integration.php_title') }}</h3>
                <pre><code>{{ $phpExample }}</code></pre>
            </article>
        </section>
    </div>
</x-filament-panels::page>
