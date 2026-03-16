# Complete Guide: Database Backup to Google Drive (Laravel)

## 1) Overview
This project now supports exporting the database (`MySQL` or `PostgreSQL`) to a local `.sql` file in `storage/app/backups`, then uploading it to Google Drive.

Authentication method is **OAuth2 + Refresh Token** (not Service Account), because personal Gmail accounts typically run into quota/storage limitations with Service Accounts on My Drive.

## 2) What was implemented in this project
- Main backup command:
  - `backup:database-to-google-drive`
- Diagnostic command:
  - `backup:diagnose-drive`
- One-time OAuth setup command:
  - `backup:google-oauth-setup`
- A Dashboard button to trigger backup manually.
- Daily scheduling via Laravel Scheduler.

### Key files
- `app/Services/DatabaseBackupService.php`
- `app/Console/Commands/BackupDatabaseToGoogleDrive.php`
- `app/Console/Commands/DiagnoseGoogleDrive.php`
- `app/Console/Commands/GoogleOAuthSetup.php`
- `resources/views/dashboard/index.blade.php`
- `routes/web.php`
- `routes/console.php`
- `config/services.php`
- `.env`

---

## 3) Prerequisites
1. A Google account (Gmail).
2. A working Laravel project.
3. Database dump tools available:
   - MySQL: `mysqldump`
   - PostgreSQL: `pg_dump`
4. Composer dependencies installed (including `google/apiclient`).

---

## 4) Google Cloud Project setup from scratch

### 4.1 Create a project
1. Open: `https://console.cloud.google.com/`
2. Click `Create Project`
3. Choose a project name (example: `Boon`)
4. Open the newly created project.

### 4.2 Enable Google Drive API
1. Go to `APIs & Services` -> `Library`
2. Search for `Google Drive API`
3. Click `Enable`.

### 4.3 Configure OAuth Consent Screen
1. Go to `APIs & Services` -> `OAuth consent screen`
2. Choose app type:
   - Usually `External`
3. Set app name (example: `Boon`)
4. Set support email
5. Save.

#### Important in Testing mode
- Add your email under `Test users` (example: `kirolossoftware@gmail.com`)
- Otherwise you will get:
  - `Error 403: access_denied`

### 4.4 Create OAuth Client ID
1. Go to `APIs & Services` -> `Credentials`
2. `Create Credentials` -> `OAuth client ID`
3. Type: `Web application`
4. In `Authorized redirect URIs`, add:
   - `http://127.0.0.1:8099`
5. Save and copy:
   - `Client ID`
   - `Client Secret`

---

## 5) Prepare your Google Drive folder
1. Open Google Drive.
2. Create a folder (example: `backup`).
3. Open the folder and copy the ID from URL:
   - Example: `https://drive.google.com/drive/folders/<FOLDER_ID>`
4. Use this ID in `.env`.

---

## 6) Configure `.env`
Set the following values:

```dotenv
GOOGLE_DRIVE_CLIENT_ID=your_client_id.apps.googleusercontent.com
GOOGLE_DRIVE_CLIENT_SECRET=your_client_secret
GOOGLE_DRIVE_REFRESH_TOKEN=
GOOGLE_DRIVE_FOLDER_ID=your_drive_folder_id

MYSQLDUMP_PATH=C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysqldump.exe
PG_DUMP_PATH=
```

> If you use PostgreSQL, set `PG_DUMP_PATH` accordingly.

---

## 7) Get Refresh Token (one-time)
Run:

```bash
php artisan optimize:clear
php artisan backup:google-oauth-setup
```

What happens:
1. Browser opens Google authorization page.
2. After approval, Google redirects to:
   - `http://127.0.0.1:8099/?code=...`
3. Command captures the code automatically and stores `GOOGLE_DRIVE_REFRESH_TOKEN` in `.env`.

---

## 8) Validate configuration
Run:

```bash
php artisan backup:diagnose-drive
```

Expected result:
- OAuth token refresh succeeds
- Upload test succeeds

> Note: with `drive.file` scope, folder metadata may sometimes return 404, but if upload test succeeds, setup is valid.

---

## 9) Run backup and upload

### Manually from terminal
```bash
php artisan backup:database-to-google-drive
```

### From Dashboard button
- Button label: `رفع النسخة الاحتياطية`
- It runs the same service and shows success with Google file ID.

---

## 10) Scheduling (Scheduler + Cron)
A daily schedule is already configured in `routes/console.php`.

On Linux server, run cron every minute:

```bash
* * * * * php /path/to/project/artisan schedule:run >> /dev/null 2>&1
```

On Windows Task Scheduler:
- Execute every minute:
  - `php artisan schedule:run`

---

## 11) Restore backup

### MySQL
```bash
mysql -u root -p boon < storage/app/backups/boon-YYYY-MM-DD_HH-mm-ss.sql
```

### PostgreSQL
```bash
psql -U postgres -d boon -f storage/app/backups/boon-YYYY-MM-DD_HH-mm-ss.sql
```

---

## 12) Common errors and fixes

### A) `redirect_uri_mismatch`
- Cause: Redirect URI in Google Cloud does not match app URI.
- Fix: add exactly:
  - `http://127.0.0.1:8099`

### B) `Error 403: access_denied` (app not verified / testers)
- Cause: app is in Testing mode and your email is not in Test Users.
- Fix: add your email in OAuth Consent Screen -> Test users.

### C) `mysqldump is not recognized`
- Cause: binary is not in PATH.
- Fix: set full `MYSQLDUMP_PATH` in `.env`.

### D) Folder `File not found`
- Cause: wrong folder ID or insufficient access for authorized account.
- Fix: verify `GOOGLE_DRIVE_FOLDER_ID` and account access.

### E) `Service Accounts do not have storage quota`
- Cause: using Service Account with My Drive.
- Fix in this project: use OAuth2 refresh token (already implemented).

---

## 13) Security notes
1. Never commit `.env`.
2. Never share `Client Secret` or `Refresh Token`.
3. If any secret was exposed:
   - Regenerate Client Secret
   - Re-run OAuth setup to get a new refresh token
4. Use least privilege scopes (`drive.file` is used here).

---

## 14) Daily command cheatsheet
```bash
php artisan optimize:clear
php artisan backup:google-oauth-setup
php artisan backup:diagnose-drive
php artisan backup:database-to-google-drive
```

---

## 15) Quick summary
- Configure Google Cloud + OAuth Consent + Test user
- Create OAuth client (Web app) with correct Redirect URI
- Fill `.env` values
- Run `backup:google-oauth-setup` to save refresh token
- Run `backup:diagnose-drive`
- Run `backup:database-to-google-drive`
- Use Dashboard button or scheduler for recurring backups
