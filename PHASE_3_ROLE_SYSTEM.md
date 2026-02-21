# PHASE 3: ROLE SYSTEM - COMPLETE ✅

## Overview
Complete role-based access control (RBAC) system with middleware protection, seeders, and helper methods.

---

## Components Created

### 1. **Role Seeder** (`database/seeders/RoleSeeder.php`)
Seeds three roles to the database:

```php
// Created Roles:
- admin      (مدير)      → Full system access
- sales      (مبيعات)    → Create invoices, quick sales, manage clients
- accountant (محاسب)     → Manage payments, reports, expenses
```

**Features:**
- Uses `firstOrCreate()` to prevent duplicates
- Bilingual names (English + Arabic)
- Descriptions for each role

### 2. **Database Seeder** (`database/seeders/DatabaseSeeder.php`)
Calls RoleSeeder and creates test users with assigned roles:

```php
// Created Users:
Email: admin@boon.local       → Role: Admin
Email: sales@boon.local       → Role: Sales
Email: accountant@boon.local  → Role: Accountant

Default password: 'password'
```

**Features:**
- Calls RoleSeeder first
- Creates users with roles
- Uses bilingual names
- Uses `firstOrCreate()` to prevent duplicates on re-seed

### 3. **CheckRole Middleware** (`app/Http/Middleware/CheckRole.php`)
Validates user roles on protected routes.

```php
// Usage in routes:
Route::get('/admin', AdminController::class)->middleware('role:admin');
Route::get('/sales', SalesController::class)->middleware('role:sales,admin');
Route::get('/accounting', AccountingController::class)->middleware('role:accountant,admin');
```

**Logic Flow:**
1. Check if user is authenticated → Redirect to login if not
2. Check if user has a role assigned → Abort 403 if not
3. Check if user's role matches allowed roles → Abort 403 if no match
4. Allow access if role matches

**Error Messages (in Arabic):**
- "المستخدم غير مخول للدخول إلى هذه الصفحة" (User not authorized to access this page)
- "لا توجد لديك الصلاحيات المطلوبة للوصول إلى هذه الصفحة" (You don't have required permissions)

### 4. **Middleware Registration** (`bootstrap/app.php`)
Registered as route middleware alias:

```php
$middleware->alias([
    'role' => \App\Http\Middleware\CheckRole::class,
]);
```

---

## Database Roles Table Structure

```sql
roles
├── id (bigint, PK)
├── name (string, UNIQUE) - admin, sales, accountant
├── name_ar (string, UNIQUE) - مدير, مبيعات, محاسب
├── description (text)
├── created_at (timestamp)
└── updated_at (timestamp)
```

---

## User-Role Relationship

**Extension to users table:**
```sql
users
├── id
├── name
├── email
├── password
├── role_id (FK → roles.id, nullable)
└── ...
```

---

## Role-Based Access Methods

### User Model Helper Methods

```php
// Check specific role
$user->hasRole('admin');        // true/false
$user->hasRole('sales');        // true/false
$user->hasRole('accountant');   // true/false

// Convenience methods
if ($user->isAdmin()) {}        // Check if admin
if ($user->isSales()) {}        // Check if sales
if ($user->isAccountant()) {}   // Check if accountant

// Get user's role
$user->role->name;              // 'admin', 'sales', 'accountant'
$user->role->name_ar;           // 'مدير', 'مبيعات', 'محاسب'
```

---

## How to Protect Routes

### Example 1: Single Role
```php
// Only admins can access
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
    ->middleware('role:admin');
```

### Example 2: Multiple Roles (OR logic)
```php
// Admins and sales can access
Route::get('/invoices', [InvoiceController::class, 'index'])
    ->middleware('role:sales,admin');

// Admins and accountants can access
Route::get('/reports', [ReportController::class, 'index'])
    ->middleware('role:accountant,admin');
```

### Example 3: Route Groups with Role Protection
```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
    Route::get('/admin/users', [UserController::class, 'list']);
    Route::post('/admin/roles', [RoleController::class, 'store']);
});

Route::middleware(['auth', 'role:sales'])->group(function () {
    Route::get('/sales/invoices', [InvoiceController::class, 'index']);
    Route::post('/sales/quick-sale', [QuickSaleController::class, 'store']);
});

Route::middleware(['auth', 'role:accountant,admin'])->group(function () {
    Route::get('/accounting/reports', [ReportController::class, 'index']);
    Route::post('/accounting/payments', [PaymentController::class, 'store']);
});
```

### Example 4: Authorization in Controllers
```php
public function store(StoreInvoiceRequest $request)
{
    // Check role in controller
    if ($request->user()->isAdmin() || $request->user()->isSales()) {
        // Proceed
    } else {
        abort(403);
    }
}
```

---

## Seeding Workflow

### Step 1: Run Migrations
```bash
php artisan migrate
```
This creates:
- roles table
- Add role_id column to users table

### Step 2: Run Seeders
```bash
php artisan db:seed
# OR specifically
php artisan db:seed --class=DatabaseSeeder
```

This creates:
- 3 roles (admin, sales, accountant)
- 3 test users with assigned roles

### Step 3: Verify in Database
```bash
mysql> SELECT * FROM roles;
mysql> SELECT id, name, email, role_id FROM users;
```

---

## Test Login Flow

### Test Admin Access
```text
Email: admin@boon.local
Password: password
Access: All admin routes with 'role:admin' middleware
```

### Test Sales Access
```text
Email: sales@boon.local
Password: password
Access: Sale-related routes with 'role:sales' middleware
Can also access shared routes: 'role:sales,admin'
```

### Test Accountant Access
```text
Email: accountant@boon.local
Password: password
Access: Accounting routes with 'role:accountant' middleware
Can also access shared routes: 'role:accountant,admin'
```

---

## Authorization Flow Diagram

```
                    User Makes Request
                            ↓
                ┌───────────────────────┐
                │ Is User Authenticated?│
                └───────────┬───────────┘
                            ↓
                    YES ← → NO (Redirect to /login)
                            ↓
                ┌───────────────────────┐
                │ Does User Have Role?  │
                └───────────┬───────────┘
                            ↓
                YES ← → NO (Abort 403: No Role)
                            ↓
    ┌───────────────────────────────────────────┐
    │ Is User's Role in Allowed Roles?          │
    │ e.g., middleware('role:admin,sales')      │
    └───────────┬───────────────────────────────┘
                ↓
    YES ← → NO (Abort 403: Insufficient Permissions)
                ↓
        ✅ Access Granted
            ↓
        Continue to Controller/View
```

---

## Project Structure

```
/app
├── Http
│   └── Middleware
│       └── CheckRole.php ✅

/database
├── seeders
│   ├── RoleSeeder.php ✅
│   └── DatabaseSeeder.php ✅
│       (updated to call RoleSeeder)
└── migrations
    └── add_role_to_users_table.php
       (already created in PHASE 1)

/app/Models
└── Role.php ✅ (created in PHASE 2)
└── User.php ✅ (extended in PHASE 2)

/bootstrap
└── app.php ✅ (middleware registered)
```

---

## Key Features

✅ **Three-Tier RBAC System**
- Admin: Full access (مدير)
- Sales: Invoice & customer management (مبيعات)
- Accountant: Financial & payment management (محاسب)

✅ **Middleware Protection**
- Route-level access control
- Flexible multi-role support (OR logic)
- Arabic error messages

✅ **Helper Methods**
- User model: hasRole(), isAdmin(), isSales(), isAccountant()
- Easy role checking in controllers

✅ **Dual Language Support**
- English role names (admin, sales, accountant)
- Arabic role names (مدير, مبيعات, محاسب)
- Arabic error messages

✅ **No Duplicates**
- firstOrCreate() prevents duplicate seeding
- Safe to re-run seeders

✅ **Test Users**
- 3 pre-created users with assigned roles
- Default password: 'password'
- For testing each role's access

---

## Usage Examples

### Check user role in blade template:
```blade
@if($user->isAdmin())
    <a href="/admin">Admin Panel</a>
@endif

@if($user->isSales())
    <a href="/invoices/create">Create Invoice</a>
@endif

@can('access-reports')
    <a href="/reports">Reports</a>
@endif
```

### Check user role in controller:
```php
public function store(Request $request)
{
    if (!$request->user()->isAdmin()) {
        abort(403);
    }
    // Proceed with admin action
}
```

### Protect entire route group:
```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
});
```

---

## Files Modified/Created in PHASE 3

### ✅ Created:
- `app/Http/Middleware/CheckRole.php` - Role checking middleware
- `database/seeders/RoleSeeder.php` - Seed roles table

### ✅ Updated:
- `bootstrap/app.php` - Register middleware alias
- `database/seeders/DatabaseSeeder.php` - Seed roles and users
- `app/Models/User.php` - Already has role relationship (from PHASE 2)
- `app/Models/Role.php` - Already configured (from PHASE 2)

---

## Ready for Implementation

### Before running migrations/seeders, ensure:
✅ Database is created (e.g., `boon_retail`)
✅ `.env` configured with correct DB credentials
✅ All PHASE 1 migrations exist
✅ All PHASE 2 models exist

### To initialize the system:
```bash
# Run migrations
php artisan migrate

# Seed roles and test users
php artisan db:seed

# Verify
php artisan tinker
> Role::all()
> User::with('role')->get()
```

---

## Next Phase: PHASE 4

**PHASE 4: SERVICE LAYER** will implement:
- InvoiceService - Handle invoice creation with stock deduction
- ProductService - Manage product operations
- PurchaseService - Handle purchases with stock increase
- ReportService - Generate financial reports

All business logic will be moved to services, keeping controllers thin.

---

**Date:** February 19, 2026
**Status:** ✅ PHASE 3 COMPLETE - AWAITING CONFIRMATION FOR PHASE 4
