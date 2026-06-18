# نشر نظام إدارة الطلبات على cPanel

الرابط المقترح للنظام:

```text
https://leadcenter.watheeqa.app
```

بعد النشر سيكون رابط استقبال الطلبات:

```text
https://leadcenter.watheeqa.app/api/leads
```

## تنبيه مهم عن SSL

الخطأ الظاهر في المتصفح:

```text
NET::ERR_CERT_COMMON_NAME_INVALID
```

يعني أن شهادة SSL الحالية لا تشمل الدومين `leadcenter.watheeqa.app`. الحل من cPanel:

1. تأكد أن Subdomain باسم `leadcenter` موجود ويشير إلى مجلد النظام الصحيح.
2. افتح `SSL/TLS Status`.
3. اختر `leadcenter.watheeqa.app`.
4. شغل `Run AutoSSL`.
5. لا تعتمد على النظام إنتاجياً قبل أن يفتح الرابط بدون تحذير أمان.

## 1. قاعدة MySQL

من cPanel افتح `MySQL Databases` وأنشئ:

```text
Database: CPANELUSER_central_leads
User:     CPANELUSER_central_user
```

اربط المستخدم بالقاعدة وأعطه `ALL PRIVILEGES`.

## 2. ملف البيئة

انسخ:

```text
.env.cpanel.example
```

إلى:

```text
.env
```

ثم عدل القيم الحقيقية:

```env
APP_URL=https://leadcenter.watheeqa.app

DB_DATABASE=CPANELUSER_central_leads
DB_USERNAME=CPANELUSER_central_user
DB_PASSWORD=PUT_DATABASE_PASSWORD_HERE

MAIL_HOST=mail.watheeqa.app
MAIL_USERNAME=no-reply@watheeqa.app
MAIL_PASSWORD=PUT_EMAIL_PASSWORD_HERE
MAIL_FROM_ADDRESS="no-reply@watheeqa.app"
MAIL_EHLO_DOMAIN=watheeqa.app
```

استعادة كلمة المرور تعتمد على هذه الإعدادات. إذا لم يعمل SMTP فلن يصل رابط الاستعادة.

## 3. طريقة الرفع الصحيحة

الخيار الأفضل أن يكون Document Root للدومين الفرعي هو:

```text
/home/CPANELUSER/central-leads/public
```

وأن يكون كامل مشروع Laravel هنا:

```text
/home/CPANELUSER/central-leads
```

إذا لم تستطع تغيير Document Root، ارفع المشروع خارج `public_html` وانسخ محتوى مجلد `public` فقط إلى:

```text
/home/CPANELUSER/public_html/leadcenter
```

ثم عدل `index.php` داخل `public_html/leadcenter`:

```php
require __DIR__.'/../../central-leads/vendor/autoload.php';
$app = require_once __DIR__.'/../../central-leads/bootstrap/app.php';
```

## 4. أوامر بعد الرفع

من Terminal داخل cPanel وفي مجلد المشروع:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize
```

ثم اختبر البريد:

```bash
php artisan app:send-test-mail your-email@example.com
```

إذا فشل الاختبار، أصلح إعدادات `MAIL_*` في `.env` ثم نفذ:

```bash
php artisan optimize:clear
```

## 5. الدخول واستعادة كلمة المرور

لوحة التحكم:

```text
https://leadcenter.watheeqa.app/admin
```

بيانات الدخول التجريبية بعد seed:

```text
admin@example.com
password
```

غير كلمة المرور مباشرة بعد أول دخول.

استعادة كلمة المرور:

```text
https://leadcenter.watheeqa.app/admin/password-reset/request
```

## 6. الربط مع فورم خارجي

في لوحة التحكم:

1. أنشئ الشركة.
2. أنشئ الموقع.
3. انسخ مفتاح API عند إنشاء الموقع. إذا احتجته لاحقًا استخدم `Regenerate API Key` وانسخ المفتاح الجديد فورًا.
4. أنشئ الفورم أو استخدم `form_key` ثابت.

ثم في الموقع الخارجي أرسل الطلب إلى:

```text
https://leadcenter.watheeqa.app/api/leads
```

مع الهيدر:

```http
Authorization: Bearer SITE_API_KEY
Content-Type: application/json
```

مثال جسم الطلب:

```json
{
  "form_key": "contact_main",
  "form_name": "نموذج التواصل",
  "form_type": "contact",
  "name": "Ahmed",
  "email": "ahmed@example.com",
  "phone": "+966500000000",
  "message": "طلب تجربة",
  "page_url": "https://www.dabt.sa/contact"
}
```

## 7. الحماية الموجودة

- تسجيل دخول وخروج للوحة الإدارة.
- استعادة كلمة المرور بالبريد.
- أدوار مستخدمين: `admin`, `manager`, `agent`.
- إدارة المستخدمين متاحة للـ `admin` فقط.
- مفتاح API مستقل لكل موقع.
- رفض الطلبات من المواقع غير الفعالة.
- Rate limit على `/api/leads`.
- Security headers أساسية.
- جلسات مشفرة وآمنة في إعداد cPanel.
- `APP_DEBUG=false` للإنتاج.
