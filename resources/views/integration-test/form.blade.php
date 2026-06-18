<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>اختبار ربط فورم خارجي</title>
    <style>
        body {
            margin: 0;
            background: #f6f7f9;
            color: #111827;
            font-family: Tahoma, Arial, sans-serif;
        }

        main {
            max-width: 760px;
            margin: 40px auto;
            padding: 0 16px;
        }

        .panel {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 24px;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 24px;
        }

        p {
            color: #4b5563;
            line-height: 1.8;
        }

        label {
            display: block;
            margin: 14px 0 6px;
            font-weight: 700;
        }

        input,
        textarea {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 10px 12px;
            font: inherit;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        button {
            margin-top: 18px;
            border: 0;
            border-radius: 6px;
            background: #2563eb;
            color: #ffffff;
            padding: 11px 18px;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }

        pre {
            direction: ltr;
            text-align: left;
            white-space: pre-wrap;
            background: #111827;
            color: #e5e7eb;
            border-radius: 8px;
            padding: 14px;
            margin-top: 18px;
        }

        .note {
            border-inline-start: 4px solid #2563eb;
            background: #eff6ff;
            padding: 10px 12px;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <main>
        <section class="panel">
            <h1>اختبار ربط فورم خارجي</h1>
            <p class="note">
                هذه الصفحة تحاكي فورم موجود في موقع خارجي وترسل الطلب فعلياً إلى
                <code>{{ url('/api/leads') }}</code>.
                مفتاح API الظاهر هنا للتجربة فقط. في المواقع الحقيقية ضعه داخل PHP handler أو WordPress backend وليس داخل JavaScript.
            </p>

            <form id="leadForm">
                <label for="api_key">API Key</label>
                <input id="api_key" name="api_key" value="sk_demo_123456789" required>

                <label for="form_key">form_key</label>
                <input id="form_key" name="form_key" value="contact_main" required>

                <label for="name">الاسم</label>
                <input id="name" name="name" value="Test Visitor" required>

                <label for="email">البريد الإلكتروني</label>
                <input id="email" name="email" type="email" value="visitor@example.com">

                <label for="phone">الهاتف</label>
                <input id="phone" name="phone" value="+966500000000">

                <label for="message">الرسالة</label>
                <textarea id="message" name="message">طلب تجربة ربط من فورم خارجي.</textarea>

                <button type="submit">إرسال الطلب إلى النظام المركزي</button>
            </form>

            <pre id="result">النتيجة ستظهر هنا بعد الإرسال.</pre>
        </section>
    </main>

    <script>
        const form = document.getElementById('leadForm');
        const result = document.getElementById('result');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const data = Object.fromEntries(new FormData(form).entries());

            const payload = {
                form_key: data.form_key,
                form_name: 'Integration Test Form',
                form_type: 'contact',
                name: data.name,
                email: data.email,
                phone: data.phone,
                message: data.message,
                page_url: window.location.href,
                raw_data: data,
            };

            result.textContent = 'Sending...';

            try {
                const response = await fetch('{{ url('/api/leads') }}', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${data.api_key}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const body = await response.json();

                result.textContent = JSON.stringify({
                    http_status: response.status,
                    response: body,
                }, null, 2);
            } catch (error) {
                result.textContent = JSON.stringify({
                    error: error.message,
                }, null, 2);
            }
        });
    </script>
</body>
</html>
