# ربط فورم موقع ضبط مع Central Leads Management System

الفورم الحالي في `11-contact.html` يرسل البيانات إلى:

```js
fetch('send-contact.php', ...)
```

لذلك لا تحتاج تغيير HTML غالباً. الربط الصحيح يكون داخل `send-contact.php` بحيث يرسل نسخة من الطلب إلى نظام Laravel المركزي.

## 1. جهز النظام المركزي

من لوحة النظام المركزي:

1. افتح `Sites`.
2. أنشئ موقعاً باسم `ضبط`.
3. ضع الرابط: `https://www.dabt.sa`.
4. انسخ مفتاح API عند إنشاء الموقع. إذا احتجته لاحقًا استخدم `Regenerate API Key` وانسخ المفتاح الجديد فورًا.
5. افتح `Forms`.
6. أنشئ فورم:

```text
site: ضبط
form_key: contact_main
name: نموذج التواصل - ضبط
type: contact
status: active
```

مهم: لا تستخدم `http://127.0.0.1:8000/api/leads` من موقع cPanel، لأن هذا العنوان يشير إلى جهازك أنت وليس إلى السيرفر. يجب أن يكون النظام المركزي منشوراً على رابط عام مثل:

```text
https://leads.example.com/api/leads
```

## 2. عدل `smtp-config.php`

أضف هذا المفتاح داخل المصفوفة الرئيسية، بجانب `site_name` و `smtp`:

```php
'central_leads' => [
    'enabled' => true,
    'endpoint' => 'https://YOUR-CENTRAL-SYSTEM-DOMAIN.com/api/leads',
    'api_key' => 'PASTE_SITE_API_KEY_HERE',
    'form_key' => 'contact_main',
    'form_name' => 'نموذج التواصل - ضبط',
    'form_type' => 'contact',
    'timeout' => 5,
],
```

بدل:

- `YOUR-CENTRAL-SYSTEM-DOMAIN.com` برابط نظام Laravel المنشور.
- `PASTE_SITE_API_KEY_HERE` بالمفتاح الذي ظهر عند إنشاء الموقع أو عند تجديد المفتاح من لوحة النظام المركزي.

## 3. عدل `send-contact.php`

بعد تعريف `$messageData = [...]` وقبل `try { smtpSend(...) ... }` أضف:

```php
try {
    sendCentralLead($config, $payload, $mailData);
} catch (Throwable $exception) {
    logSmtpFailure($config, [
        'reference' => $reference,
        'subject' => $subjectLabel,
        'name' => $name,
        'organization' => $organization,
        'phone' => $phone,
        'email' => (string) $email,
        'page_url' => $pageUrl,
        'error' => 'Central leads API: ' . $exception->getMessage(),
    ]);
}
```

ثم أضف هذه الدالة في آخر الملف قبل نهاية PHP أو بعد الدوال الموجودة:

```php
function sendCentralLead(array $config, array $payload, array $mailData): void
{
    $central = isset($config['central_leads']) && is_array($config['central_leads'])
        ? $config['central_leads']
        : [];

    if (empty($central['enabled'])) {
        return;
    }

    $endpoint = isset($central['endpoint']) ? trim((string) $central['endpoint']) : '';
    $apiKey = isset($central['api_key']) ? trim((string) $central['api_key']) : '';

    if ($endpoint === '' || $apiKey === '') {
        throw new RuntimeException('Central leads endpoint or API key is missing.');
    }

    $leadData = [
        'form_key' => isset($central['form_key']) ? (string) $central['form_key'] : 'contact_main',
        'form_name' => isset($central['form_name']) ? (string) $central['form_name'] : 'نموذج التواصل',
        'form_type' => isset($central['form_type']) ? (string) $central['form_type'] : 'contact',
        'name' => $mailData['name'],
        'email' => $mailData['email'],
        'phone' => $mailData['phone'],
        'message' => $mailData['message'],
        'page_url' => $mailData['page_url'],
        'raw_data' => [
            'reference' => $mailData['reference'],
            'subject' => isset($payload['subject']) ? $payload['subject'] : null,
            'subject_label' => $mailData['subject_label'],
            'job_title' => $mailData['job_title'],
            'organization' => $mailData['organization'],
            'site_name' => $mailData['site_name'],
            'page_title' => $mailData['page_title'],
            'submitted_at' => $mailData['submitted_at'],
            'ip_address' => $mailData['ip_address'],
            'user_agent' => $mailData['user_agent'],
        ],
    ];

    $json = json_encode($leadData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($json === false) {
        throw new RuntimeException('Unable to encode central lead payload.');
    }

    if (function_exists('curl_init')) {
        $ch = curl_init($endpoint);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => isset($central['timeout']) ? (int) $central['timeout'] : 5,
        ]);

        $response = curl_exec($ch);
        $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($response === false || $statusCode < 200 || $statusCode >= 300) {
            throw new RuntimeException('Central leads request failed. HTTP ' . $statusCode . ' ' . $error);
        }

        return;
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $apiKey,
            ]),
            'content' => $json,
            'timeout' => isset($central['timeout']) ? (int) $central['timeout'] : 5,
            'ignore_errors' => true,
        ],
    ]);

    $response = @file_get_contents($endpoint, false, $context);

    if ($response === false) {
        throw new RuntimeException('Central leads request failed.');
    }
}
```

## 4. الاختبار

1. احفظ `smtp-config.php`.
2. احفظ `send-contact.php`.
3. افتح صفحة التواصل في الموقع.
4. أرسل طلباً تجريبياً.
5. إذا وصل الإيميل ولم يظهر الطلب في النظام المركزي، افتح ملف:

```text
smtp-error.log
```

وابحث عن سطر يبدأ بـ:

```text
Central leads API:
```

## ملاحظات مهمة

- فشل إرسال الطلب إلى النظام المركزي لن يمنع إرسال الإيميل للمستخدمين.
- لا تضع `api_key` في JavaScript أو HTML.
- غيّر كلمة مرور SMTP إذا ظهرت في صورة أو محادثة أو ملف عام.
