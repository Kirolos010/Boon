# دليل كامل: نسخ قاعدة البيانات ورفعها على Google Drive (Laravel)

## 1) نظرة عامة
هذا المشروع يدعم الآن نسخ قاعدة البيانات (`MySQL` أو `PostgreSQL`) إلى ملف `.sql` داخل `storage/app/backups` ثم رفعه إلى Google Drive.

تم اعتماد **OAuth2 + Refresh Token** (وليس Service Account) لأن حسابات Gmail الشخصية غالبا لا تدعم Shared Drives بشكل عملي في كل الحالات، وService Account يظهر معه خطأ quota في My Drive.

## 2) ما الذي تم تنفيذه داخل المشروع
- أمر النسخ والرفع:
  - `backup:database-to-google-drive`
- أمر التشخيص:
  - `backup:diagnose-drive`
- أمر تجهيز OAuth مرة واحدة:
  - `backup:google-oauth-setup`
- زر داخل الهوم (Dashboard) لتشغيل النسخ يدويًا.
- جدولة يومية عبر Laravel Scheduler.

### الملفات المهمة
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

## 3) المتطلبات قبل البدء
1. حساب Google (Gmail).
2. مشروع Laravel يعمل محليًا.
3. أدوات dump متاحة:
   - MySQL: `mysqldump`
   - PostgreSQL: `pg_dump`
4. Composer dependencies موجودة (ومنها `google/apiclient`).

---

## 4) إعداد Google Cloud Project من الصفر

### 4.1 إنشاء مشروع
1. افتح: `https://console.cloud.google.com/`
2. Create Project
3. اختر اسم المشروع (مثال: `Boon`)
4. ادخل المشروع بعد إنشائه.

### 4.2 تفعيل Google Drive API
1. من القائمة: `APIs & Services` -> `Library`
2. ابحث عن `Google Drive API`
3. اضغط `Enable`.

### 4.3 إعداد OAuth Consent Screen
1. اذهب إلى: `APIs & Services` -> `OAuth consent screen`
2. اختر نوع التطبيق:
   - غالبًا `External`
3. اكتب اسم التطبيق (مثال: `Boon`)
4. أضف بريدك في support email
5. احفظ.

#### مهم في وضع Testing
- أضف بريدك ضمن `Test users` (مثال: `kirolossoftware@gmail.com`)
- بدون هذه الخطوة سيظهر:
  - `Error 403: access_denied`

### 4.4 إنشاء OAuth Client ID
1. اذهب إلى: `APIs & Services` -> `Credentials`
2. `Create Credentials` -> `OAuth client ID`
3. النوع: `Web application`
4. في `Authorized redirect URIs` أضف:
   - `http://127.0.0.1:8099`
5. احفظ ثم انسخ:
   - `Client ID`
   - `Client Secret`

---

## 5) إعداد فولدر Google Drive
1. افتح Google Drive.
2. أنشئ فولدر (مثلا: `backup`).
3. افتح الفولدر وانسخ الـ ID من الرابط:
   - مثال: `https://drive.google.com/drive/folders/<FOLDER_ID>`
4. استخدم هذا الـ ID في `.env`.

---

## 6) إعداد `.env`
ضع القيم التالية:

```dotenv
GOOGLE_DRIVE_CLIENT_ID=your_client_id.apps.googleusercontent.com
GOOGLE_DRIVE_CLIENT_SECRET=your_client_secret
GOOGLE_DRIVE_REFRESH_TOKEN=
GOOGLE_DRIVE_FOLDER_ID=your_drive_folder_id

MYSQLDUMP_PATH=C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysqldump.exe
PG_DUMP_PATH=
```

> لو تستخدم PostgreSQL، ضع مسار `pg_dump` في `PG_DUMP_PATH`.

---

## 7) استخراج Refresh Token (مرة واحدة فقط)
شغل:

```bash
php artisan optimize:clear
php artisan backup:google-oauth-setup
```

ما يحدث:
1. يفتح المتصفح على صفحة موافقة Google.
2. بعد الموافقة، Google ترجع إلى:
   - `http://127.0.0.1:8099/?code=...`
3. الأمر يلتقط `code` تلقائيا ويحفظ `GOOGLE_DRIVE_REFRESH_TOKEN` في `.env`.

---

## 8) فحص الإعدادات
شغل:

```bash
php artisan backup:diagnose-drive
```

النتيجة المتوقعة:
- OAuth token refreshed successfully
- Upload test passed

> ملاحظة: أحيانا قراءة metadata للفولدر قد تعطي 404 مع scope `drive.file`، لكن إن اختبار الرفع نجح فالإعداد صحيح.

---

## 9) التشغيل الفعلي للنسخ والرفع

### يدويًا من التيرمنال
```bash
php artisan backup:database-to-google-drive
```

### من زر الهوم (Dashboard)
- زر: `رفع النسخة الاحتياطية`
- يشغل نفس الخدمة ويعرض رسالة نجاح بها Google file ID.

---

## 10) الجدولة (Scheduler + Cron)
الجدولة موجودة في `routes/console.php` يوميًا (مثلا 02:00).

على السيرفر شغل Cron كل دقيقة:

```bash
* * * * * php /path/to/project/artisan schedule:run >> /dev/null 2>&1
```

على Windows Task Scheduler:
- نفذ كل دقيقة:
  - `php artisan schedule:run`

---

## 11) استرجاع النسخة الاحتياطية

### MySQL
```bash
mysql -u root -p boon < storage/app/backups/boon-YYYY-MM-DD_HH-mm-ss.sql
```

### PostgreSQL
```bash
psql -U postgres -d boon -f storage/app/backups/boon-YYYY-MM-DD_HH-mm-ss.sql
```

---

## 12) أشهر المشاكل وحلها

### A) `redirect_uri_mismatch`
- السبب: Redirect URI في Google Cloud لا يطابق المستخدم في التطبيق.
- الحل: أضف بالضبط:
  - `http://127.0.0.1:8099`

### B) `Error 403: access_denied` (app not verified / testers)
- السبب: التطبيق في وضع Testing والبريد غير مضاف في Test Users.
- الحل: أضف بريدك في OAuth Consent Screen -> Test users.

### C) `mysqldump is not recognized`
- السبب: المسار غير موجود في PATH.
- الحل: ضع `MYSQLDUMP_PATH` كاملا في `.env`.

### D) `File not found` للفولدر
- السبب: ID غلط أو الحساب المصرح له ليس له صلاحية على الفولدر.
- الحل: تأكد من `GOOGLE_DRIVE_FOLDER_ID` والصلاحيات.

### E) `Service Accounts do not have storage quota`
- السبب: استخدام Service Account مع My Drive.
- الحل في هذا المشروع: استخدم OAuth2 refresh token (المطبق الآن).

---

## 13) ملاحظات أمان مهمة
1. لا ترفع `.env` على GitHub.
2. لا تشارك `Client Secret` أو `Refresh Token`.
3. إذا تم تسريب أي secret:
   - اعمل Regenerate للـ Client Secret
   - أعد استخراج Refresh Token
4. استخدم صلاحيات أقل قدر ممكن (`drive.file` مستخدم هنا).

---

## 14) أوامر سريعة للاستخدام اليومي
```bash
php artisan optimize:clear
php artisan backup:google-oauth-setup
php artisan backup:diagnose-drive
php artisan backup:database-to-google-drive
```

---

## 15) ملخص سريع
- إعداد Google Cloud + OAuth Consent + Test user
- إنشاء OAuth Client (Web app) مع Redirect URI صحيح
- وضع القيم في `.env`
- تشغيل `backup:google-oauth-setup` للحصول على refresh token
- تشغيل `backup:diagnose-drive`
- تشغيل `backup:database-to-google-drive`
- الاعتماد على الزر في الهوم أو الجدولة اليومية
