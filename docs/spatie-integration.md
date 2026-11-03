# Spatie packages: integration notes

This page records which Spatie packages RizqEngine uses, why, how they are
configured and how to operate them. It covers the November 2026 integration of
backups, health monitoring, honeypot bot protection, product tags, Schema.org
structured data and spreadsheet import/export.

## Packages

| Package | Version | Used for |
|---|---|---|
| `spatie/laravel-backup` | ^10.0 | Database and uploaded-file backups, retention cleanup, backup monitoring |
| `spatie/laravel-health` | ^1.34 | Health checks, the `/health` probe and the System status screen |
| `spatie/laravel-honeypot` | ^4.6 | Bot protection on the public sign-in and password-reset forms |
| `spatie/laravel-tags` | ^4.10 | Product tags |
| `spatie/schema-org` | ^4.0 \|\| ^5.0 | JSON-LD structured data on the public landing page |
| `spatie/simple-excel` | ^3.7 | Streaming CSV/XLSX import and export |

Already installed and unchanged: `laravel-permission`, `laravel-activitylog`,
`laravel-medialibrary`, `laravel-translatable`.

### Packages not installed, and why

| Package | Reason |
|---|---|
| `spatie/laravel-sluggable` | RizqEngine has no public content URLs. Every product, category and document is reached through an authenticated, ID-based admin route. Slugs would have nothing to serve, and adding them later must not change existing URLs. Install it when a public catalogue or storefront is built. |
| `spatie/laravel-feed` | No public content (articles, news, blog posts) exists to syndicate. A feed of products or invoices would leak business data. |
| `spatie/laravel-pdf` | The app already ships two PDF engines: `barryvdh/laravel-dompdf` for business documents and `barryvdh/laravel-snappy` for barcode labels. A third engine, which needs Chromium/Browsershot or an external service, would duplicate them. The goals of the request (one reusable service, dedicated templates and authorization) were met by centralising the existing DomPDF rendering instead. See [PDF documents](#pdf-documents). |

## Installation and deployment

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate            # tags tables + new permissions
php artisan config:cache
```

Migrations added:

- `2026_11_03_000000_create_tag_tables`: the `tags` and `taggables` tables, from the package stub, plus an index on `tags.type`.
- `2026_11_03_000001_add_system_monitoring_and_export_permissions`:
  - `access_system_monitoring` is granted to Owner and Admin.
  - `export_products` is granted to Owner, Admin and Manager.
  - Super Admin passes every gate.

Fresh installs get the same grants from `PermissionsTableSeeder`.

### Scheduler and queue

The standard cron entry is required:

```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

| Task | When | Notes |
|---|---|---|
| `health:check` | every minute | Stores the latest results and e-mails failures when `HEALTH_TO_ADDRESS` is set |
| `health:schedule-check-heartbeat` | every minute | Only when `HEALTH_CHECK_SCHEDULE=true` |
| `health:queue-check-heartbeat` | every minute | Only when the queue check is on (default: any non-`sync` queue) |
| `backup:clean` | `BACKUP_CLEANUP_TIME` (01:30) | Only when `BACKUP_SCHEDULE_ENABLED=true` |
| `backup:run` | `BACKUP_SCHEDULE_TIME` (02:00) | Only when `BACKUP_SCHEDULE_ENABLED=true` |
| `backup:monitor` | `BACKUP_MONITOR_TIME` (06:00) | Only when `BACKUP_SCHEDULE_ENABLED=true` |

The queue check requires a worker (`php artisan queue:work`).

## Environment variables

All variables are listed with defaults in `.env.example`.

| Variable | Default | Purpose |
|---|---|---|
| `HONEYPOT_ENABLED` | `true` | Turn bot protection on or off |
| `HONEYPOT_SECONDS` | `1` | Minimum seconds a form must be open before it is submitted |
| `BACKUP_DISKS` | `backups` | Comma-separated destination disks |
| `BACKUP_SCHEDULE_ENABLED` | `false` | Schedule clean, run and monitor |
| `BACKUP_SCHEDULE_TIME` / `BACKUP_CLEANUP_TIME` / `BACKUP_MONITOR_TIME` | `02:00` / `01:30` / `06:00` | Daily times |
| `BACKUP_NOTIFICATION_EMAIL` | empty | Recipient of failure e-mails; no mail is sent when empty |
| `BACKUP_ARCHIVE_PASSWORD` | empty | Encrypt the zip archives |
| `BACKUP_MAX_AGE_DAYS`, `BACKUP_MAX_STORAGE_MB` | `1`, `5000` | Backup health thresholds |
| `BACKUP_KEEP_*` | 7 / 16 / 8 / 4 / 2 | Retention policy |
| `HEALTH_TO_ADDRESS` | empty | Recipient of health-failure e-mails; notifications are off when empty |
| `HEALTH_DISK_WARN_PERCENTAGE` / `HEALTH_DISK_FAIL_PERCENTAGE` | `70` / `90` | Disk-space thresholds |
| `HEALTH_CHECK_SCHEDULE` / `HEALTH_CHECK_QUEUE` / `HEALTH_CHECK_BACKUPS` | see `.env.example` | Optional checks |
| `DOCUMENTS_PDF_PAPER` / `DOCUMENTS_PDF_ORIENTATION` | `a4` / `portrait` | PDF defaults |
| `IMPORT_MAX_KB` / `IMPORT_MAX_ROWS` | `5120` / `5000` | Import limits |
| `EXPORT_DATE_FORMAT` | `Y-m-d` | Date format in exports |

These are deliberately technical settings kept in `.env`, not in the admin
Settings screen. Company identity is not duplicated anywhere: the name,
address, phone, e-mail and logo used in JSON-LD and on documents still come
from **Settings → General**.

## Backups

**What is backed up**

- The application database (gzip-compressed dump).
- `storage/app/public`, which holds product and category images, logos and uploaded documents.

The source code is not backed up; it lives in Git.

**Where backups go**

The private `backups` disk (`storage/app/backups`). It is not symlinked into
`public/` and has no URL. Add an off-site disk to `BACKUP_DISKS` for real
disaster recovery: configure it in `config/filesystems.php` with credentials in
`.env`, for example `BACKUP_DISKS=backups,s3`.

**Who can do what**

- Backups cannot be triggered, downloaded or deleted from the browser.
- The **System status** screen shows only the status: reachable, healthy, number of backups, newest backup and storage used.

**Requirements**

- `mysqldump` (or `mariadb-dump`) on the server's PATH.
- The PHP `zip` extension.

### First run (verify before scheduling)

```bash
php artisan backup:run --only-db   # check the database dump works
php artisan backup:run             # full backup
php artisan backup:list            # check it is listed and healthy
```

Then set `BACKUP_SCHEDULE_ENABLED=true` and run `php artisan config:cache`.
Cleanup (`backup:clean`) deletes old archives according to the retention
policy. It is never scheduled until you opt in.

### Restore

1. Copy the archive (`storage/app/backups/<APP_NAME>/<date>.zip`) to a safe working directory and unzip it. If `BACKUP_ARCHIVE_PASSWORD` was set, the archive is encrypted with it.
2. Restore the database. **This overwrites the current data.**
   ```bash
   gunzip db-dumps/mysql-<database>.sql.gz
   mysql -u <user> -p <database> < db-dumps/mysql-<database>.sql
   ```
3. Restore the files. Paths in the archive are relative to the project root:
   ```bash
   rsync -a <unzipped>/storage/app/public/ storage/app/public/
   ```
4. Finish:
   ```bash
   php artisan storage:link
   php artisan cache:clear
   ```

## Health monitoring

The following checks are registered in `App\Providers\HealthServiceProvider`:

- **Always:**
  - Database
  - Cache
  - Storage: write, read and delete on the `local` and `public` disks
  - Used disk space
- **Production only:** debug mode is off, and the environment is `production`.
- **Optional:** queue, schedule and backups.

**Endpoints**

- `GET /health` is public and throttled. It returns `{"healthy": true|false}` with HTTP 200 or 503, read from the stored results. It never runs checks itself (`?fresh` is ignored) and never shows check details. Point uptime monitors or load balancers at it.
- `GET /system-status` is authenticated and needs `access_system_monitoring`. It shows each check with its message and backup status, and has a **Run checks now** button. It never shows file paths, credentials or environment values.

**CLI**

```bash
php artisan health:check
php artisan health:list
```

## Bot protection (honeypot)

Protected forms:

- `/sign-in`, a Livewire component that uses `UsesSpamProtection`.
- The legacy `POST /login`.
- `POST /password/email`, the password-reset request.

A submission is rejected (blank page, or 403 on Livewire) when either:

- the hidden field is filled, or
- the form was submitted faster than `HONEYPOT_SECONDS`.

Authenticated back-office forms are not affected. To protect another public
form, add `<x-honeypot />` inside it and the `ProtectAgainstSpam` middleware
to its route. For a Livewire form, add a `HoneypotData $extraFields` property
and `<x-honeypot livewire-model="extraFields" />`, and call
`$this->protectAgainstSpam()` in the submit action.

## Product tags

Tags are free-form labels such as *promo*, *seasonal* or *fragile*. They sit
alongside the single product category; they do not replace it.

- **Manage:** in the product create and edit forms, type comma-separated tags (the Arabic comma `،` works too) or click an existing tag to add it. New tags are created on save, and matching is case-insensitive. Limits: up to 20 tags per product, 50 characters each.
- **Filter:** the product list has a **Tags** filter (`?tag=<id>`), and the product cards show the tags.
- **Import and export:** both use a `tags` column.
- **Multilingual:** a new tag is stored with the same label in en, fr and ar, so it can be found from any UI language.
- **Code:** `App\Services\Tags\ProductTagService` handles `parse()`, `sync()`, `all()` and `findOrCreate()`.

## Spreadsheet import and export

### Import

The existing import screens all accept CSV and Excel (`.xlsx`) files:

- Products
- Categories
- Customers
- Suppliers
- Drivers
- Vehicles

Files are streamed through `App\Services\Spreadsheets\SpreadsheetReader`.

Every row is validated and shown in a preview with its line number and errors.
Only valid rows are imported, in a single transaction.

Upload safety:

- The upload is validated by detected content type: `csv`, `txt` or `xlsx`.
- Size is capped by `IMPORT_MAX_KB`, and the row count by `IMPORT_MAX_ROWS`.
- Each import keeps its own permission (for example, `create_products`).

### Export

On the product list, **Export CSV** and **Export Excel** download the
currently filtered products. This needs the `export_products` permission,
because the file includes cost prices.

- **Columns** match the import template, so an exported file can be edited and re-imported.
- **Memory:** rows are read with `lazyById(500)` and written to a private temp file that is deleted after it is sent.
- **Formula injection:** cells starting with `=`, `+`, `-`, `@`, a tab or a carriage return are prefixed with `'`.

To export something else, pass any iterable of associative arrays to
`SpreadsheetExporter::download($rows, 'basename', 'csv'|'xlsx')`.

## PDF documents

Business documents (sales/invoices, POS receipts, purchases, returns,
quotations, withholding certificates) are rendered by
`App\Services\Pdf\DocumentPdfService`. It wraps the existing DomPDF engine and
supplies:

- the default paper size and orientation from `config/documents.php`
- safe file names
- `stream()` (inline preview) and `download()`

The routes are handled by `App\Http\Controllers\DocumentPdfController`, with
unchanged URLs and route names:

- `sales.pdf`
- `sales.pos.pdf`
- `purchases.pdf`
- `purchase-returns.pdf`
- `sale-returns.pdf`
- `quotations.pdf`
- `purchases.withholding-certificate`

Each action now checks the matching `show_*` permission. Previously any
signed-in user could download any document PDF. Templates are the existing
Blade views in `resources/views/*/print*.blade.php`.

To add a document:

```php
return app(DocumentPdfService::class)->stream('my.template', $data, 'my-document-'.$model->reference);
```

## Structured data (Schema.org)

The public landing page (`/`) outputs one JSON-LD `@graph` with:

- an **Organization**, from Settings → General: name, logo, e-mail, phone and address
- a **WebSite**, published by that Organization

Back-office pages are behind authentication and carry none. The data is built
in `App\Services\Seo\StructuredDataService` and rendered by
`<x-structured-data>` in `layouts/redesign`. It is JSON-encoded with
`JSON_HEX_TAG`, so values entered by admins cannot close the `<script>` tag.
