# Central Leads Management System Integration

## Endpoint

`POST https://your-system.com/api/leads`

Send the site API key in one of these headers:

```http
Authorization: Bearer YOUR_SITE_API_KEY
X-API-Key: YOUR_SITE_API_KEY
```

Successful response:

```json
{
  "success": true,
  "message": "Lead created successfully",
  "lead_id": 123
}
```

Error response:

```json
{
  "success": false,
  "message": "Invalid API key"
}
```

## PHP cURL

```php
$leadData = [
    'form_key'  => 'contact_main',
    'form_name' => 'Main Contact Form',
    'form_type' => 'contact',
    'name'      => $_POST['name'] ?? '',
    'email'     => $_POST['email'] ?? '',
    'phone'     => $_POST['phone'] ?? '',
    'message'   => $_POST['message'] ?? '',
    'page_url'  => $_POST['page_url'] ?? '',
    'raw_data'  => $_POST,
];

$ch = curl_init('https://your-system.com/api/leads');

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer YOUR_SITE_API_KEY',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($leadData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);

$response = curl_exec($ch);
curl_close($ch);
```

## HTML Form + PHP Handler

```html
<form method="post" action="/send-lead.php">
    <input type="text" name="name" required>
    <input type="email" name="email">
    <input type="tel" name="phone">
    <textarea name="message"></textarea>
    <input type="hidden" name="page_url" value="https://example.com/contact">
    <button type="submit">Send</button>
</form>
```

```php
<?php

$payload = [
    'form_key' => 'contact_main',
    'form_name' => 'Contact Form',
    'form_type' => 'contact',
    'name' => $_POST['name'] ?? null,
    'email' => $_POST['email'] ?? null,
    'phone' => $_POST['phone'] ?? null,
    'message' => $_POST['message'] ?? null,
    'page_url' => $_POST['page_url'] ?? null,
    'raw_data' => $_POST,
];

$ch = curl_init('https://your-system.com/api/leads');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'X-API-Key: YOUR_SITE_API_KEY',
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 5,
]);

curl_exec($ch);
curl_close($ch);

header('Location: /thank-you.html');
exit;
```

## WordPress

```php
wp_remote_post('https://your-system.com/api/leads', [
    'timeout' => 5,
    'headers' => [
        'Authorization' => 'Bearer YOUR_SITE_API_KEY',
        'Content-Type'  => 'application/json',
    ],
    'body' => json_encode([
        'form_key'  => 'contact_main',
        'form_name' => 'Contact Form',
        'form_type' => 'contact',
        'name'      => $name,
        'email'     => $email,
        'phone'     => $phone,
        'message'   => $message,
        'page_url'  => home_url(add_query_arg([], $_SERVER['REQUEST_URI'] ?? '')),
        'raw_data'  => $data,
    ]),
]);
```

## Laravel HTTP Client

```php
use Illuminate\Support\Facades\Http;

Http::timeout(5)
    ->withToken('YOUR_SITE_API_KEY')
    ->post('https://your-system.com/api/leads', [
        'form_key'  => 'quote_home',
        'form_name' => 'Quote Form',
        'form_type' => 'quote',
        'name'      => $request->name,
        'email'     => $request->email,
        'phone'     => $request->phone,
        'message'   => $request->message,
        'page_url'  => url()->previous(),
        'raw_data'  => $request->all(),
    ]);
```

Keep external forms resilient: use short timeouts and do not block the visitor flow if the central API is temporarily unavailable.

## Testing With A Real Site Form

1. In the admin panel, create or open a site record.
2. Copy the API key when the site is created. Existing keys are stored securely and only a preview is shown.
   If you need a new visible key later, use **Regenerate API Key** and copy the new value immediately.
3. Create a form record under that site, for example `form_key = contact_main`.
4. Add the PHP handler or WordPress example above to the external site.
5. Submit the external site form.
6. Open the central dashboard and check `Leads`.

For local simulation, use:

`http://127.0.0.1:8000/integration-test/form`

This page sends a real request to `/api/leads` using the demo API key. It is for development only. In production, keep the API key server-side in PHP, WordPress, or Laravel backend code.
