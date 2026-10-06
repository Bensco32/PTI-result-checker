# PTI Student Result Management and Result Checking System

A PHP + PostgreSQL application for the Petroleum Training Institute (PTI)
result portal. Students check results online; administrators manage students
and upload official result PDFs.

## Requirements

- PHP 8.1+ with `pdo_pgsql` enabled
- PostgreSQL 14+
- Any web server (Apache/Nginx) or the PHP built-in server

## 1. Run locally with XAMPP

1. Copy/clone this folder to `C:\xampp\htdocs\PTI_Result_Checker`.
2. In `C:\xampp\php\php.ini`, enable:
   ```
   extension=pdo_pgsql
   extension=pgsql
   ```
3. Start Apache. Open `http://localhost/PTI_Result_Checker/`.

You can also run it without Apache:

```
cd C:\path\to\PTI_Result_Checker
php -S localhost:8080
```

## 2. Create the PostgreSQL database

```
psql -U postgres -h localhost
CREATE DATABASE pti_result_checker;
CREATE USER pti_user WITH PASSWORD 'your-strong-password';
ALTER DATABASE pti_result_checker OWNER TO pti_user;
```

## 3. Create the tables

```
psql -U pti_user -h localhost -d pti_result_checker -f database/schema.sql
```

## 4. Configure database credentials

Production / hosted: set environment variables (no code changes needed):

```
DB_HOST=...
DB_PORT=5432
DB_NAME=...
DB_USER=...
DB_PASSWORD=...
```

Local development: edit `config/config.local.php` (never commit real secrets).

## 5. Create the first administrator

```
php scripts/create-admin.php --username=admin --email=admin@pti.edu.ng --name="PTI Admin" --password=ChangeMe123
```

Change the default password after the first login. Do not hard-code
credentials anywhere in the codebase.

## 6. Register a student (admin)

Log in at `/admin/login.php` → **Add Student** → fill in the form.
The admin sets an initial password for the student account.

## 7. Upload a result (admin)

`/admin/upload-result.php` → select student, session, semester, PDF → **Upload**.
PDF must be a real PDF, ≤ 5MB. It is stored with a random filename in
`uploads/results/` and linked in PostgreSQL.

## 8. Check a result (public)

Home → **Check Result** → enter matric number, session, semester → **View Result**.

## 9. Student login

`/login.php` with matric number (or email) + password →
`student/dashboard.php` shows the student's results. Each result can be viewed
in the browser, downloaded, or printed (result slip).

## 10. Deploy to Render

1. Push this project to GitHub (do **not** commit `config/config.local.php`).
2. On Render, create a **PostgreSQL** instance; note host/port/db/user/password.
3. Create a **Web Service** connected to your repo — a `Dockerfile`
   (PHP 8.2 + Apache + pdo_pgsql) is included, so Render builds it
   automatically. A `render.yaml` blueprint is also included: you can
   instead create a **Blueprint** instance which provisions both the
   web service and a free PostgreSQL database and wires the `DB_*`
   environment variables for you.
   - A start-command-only alternative (no Docker):
     `php -S 0.0.0.0:$PORT -t .` — but the Dockerfile is recommended
     because it includes `pdo_pgsql` and the Apache `uploads` protections.
4. Set environment variables on Render: `DB_HOST`, `DB_PORT`, `DB_NAME`,
   `DB_USER`, `DB_PASSWORD` from the Render PostgreSQL instance.
5. Run `database/schema.sql` against the Render database (e.g. with `psql`
   from Cloud Shell or locally), then `php scripts/create-admin.php`.
6. Ensure `uploads/results/` is writable (or use a persistent disk mounted
   at `uploads/`).

## Folder structure

```
index.php, check-result.php, result.php, login.php, ...  public pages
student/      student dashboard, results, profile, result view
admin/        admin login, dashboard, students, results, uploads
includes/     header, footer, auth guards, shared functions
config/       database.php (+ local overrides, never committed)
uploads/      results/ (PDFs, blocked from PHP execution), photos/
assets/       PTI logo, campus image, default student photo
css/, js/     preserved styles and UI script (no demo data)
database/     schema.sql
scripts/      create-admin.php
```

## Security notes

- Passwords hashed with `password_hash()` / verified with `password_verify()`.
- All SQL uses PDO prepared statements.
- CSRF tokens on all POST forms; `htmlspecialchars()` on all output.
- Admin and student pages are guarded; students cannot access other students'
  results or admin pages.
- Uploads are validated by MIME type, extension and size; files are stored
  under random names and the uploads directory disables PHP execution.
- Result downloads go through a PHP endpoint with authorization checks —
  direct file paths are not exposed.
