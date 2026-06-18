# Central Leads Management System

Laravel MVP for collecting form leads from multiple external sites through a central API and managing them in a Filament admin dashboard.

## Local Setup

```bash
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Admin panel:

`http://127.0.0.1:8000/admin`

The admin panel supports Arabic and English. Arabic is the default locale, and authenticated users can switch language from the user menu. Direct switch URLs are:

- Arabic: `/language/ar`
- English: `/language/en`

Demo admin:

- Email: `admin@example.com`
- Password: `password`

Demo site API key:

`sk_demo_123456789`

New site API keys are shown once when created or regenerated, then stored as a hash with only a short preview visible in the admin panel.

## Database

The project is intended for MySQL in production. Configure `.env` like this:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=central_leads
DB_USERNAME=root
DB_PASSWORD=
```

The current local `.env` may use SQLite for quick verification.

## API

`POST /api/leads`

Headers:

```http
Authorization: Bearer SITE_API_KEY
Content-Type: application/json
```

Alternative header:

```http
X-API-Key: SITE_API_KEY
```

Example body:

```json
{
  "form_key": "quote_home",
  "form_name": "Quote Form",
  "form_type": "quote",
  "name": "Ahmed",
  "email": "ahmed@example.com",
  "phone": "+966500000000",
  "message": "I need a quote for a company website.",
  "page_url": "https://example.com/services",
  "raw_data": {
    "service": "Website Design",
    "budget": "5000 SAR"
  }
}
```

See [docs/integration.md](docs/integration.md) for PHP, WordPress, and Laravel integration examples.
For cPanel deployment, see [docs/cpanel-deployment.md](docs/cpanel-deployment.md).

Production target prepared in `.env.cpanel.example`:

`https://leadcenter.watheeqa.app`

After configuring SMTP on cPanel, test password-reset email delivery with:

```bash
php artisan app:send-test-mail your-email@example.com
```

## Integration Test Form

Open this local test page to simulate a real external website form:

`http://127.0.0.1:8000/integration-test/form`

Use the demo API key `sk_demo_123456789`, submit the form, then check the created lead in:

`http://127.0.0.1:8000/admin/leads`
