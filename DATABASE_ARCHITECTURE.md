# Database Architecture - Coffee & Herbal Retail Management System

## Overview
This document outlines the complete database structure for the Internal Sales & Inventory Management System, designed for a coffee and herbal retail/wholesale shop.

---

## Database Tables

### 1. **ROLES** (دور المستخدم)
User role definitions for RBAC (Role-Based Access Control)

| Column | Type | Constraints | Notes |
|--------|------|-----------|-------|
| id | bigint, unsigned, auto-increment | PRIMARY KEY | |
| name | string, unique | UNIQUE | Admin, Sales, Accountant |
| name_ar | string, unique | UNIQUE | مدير, مبيعات, محاسب |
| description | text | nullable | Role description |
| timestamps | | | created_at, updated_at |

**Relationships:**
- Has Many: Users

---

### 2. **USERS** (extension - add role)
Extended users table with role assignment

| Column | Type | Constraints | Notes |
|--------|------|-----------|-------|
| role_id | bigint, unsigned | FK → roles, nullable | User's assigned role |

**Relationships:**
- Belongs To: Role
- Has Many: Invoices, Purchases, Expenses, StockMovements, Clients, Products

**Foreign Keys:**
- `role_id` → `roles.id` (nullable, cascade on delete)

---

### 3. **MAIN_CATEGORIES** (التصنيفات الرئيسية)
Primary product categories (Coffee, Herbs, etc.)

| Column | Type | Constraints | Notes |
|--------|------|-----------|-------|
| id | bigint, unsigned, auto-increment | PRIMARY KEY | |
| name | string, unique | UNIQUE | en: Coffee, Herbs |
| name_ar | string, unique | UNIQUE | ar: بن, عطارة |
| description | text | nullable | |
| description_ar | string | nullable | |
| timestamps | | | |

**Relationships:**
- Has Many: SubCategories
- Has Many: Products

---

### 4. **SUB_CATEGORIES** (التصنيفات الفرعية)
Secondary product categories under main categories

| Column | Type | Constraints | Notes |
|--------|------|-----------|-------|
| id | bigint, unsigned, auto-increment | PRIMARY KEY | |
| main_category_id | bigint, unsigned | FK, cascade delete | |
| name | string | | Brazilian, Indonesian |
| name_ar | string | | برازيلي, اندونيسي |
| description | text | nullable | |
| description_ar | string | nullable | |
| timestamps | | | |

**Indexes:**
- `UNIQUE(main_category_id, name)` - Ensure unique sub-categories per main category

**Relationships:**
- Belongs To: MainCategory
- Has Many: Products

**Foreign Keys:**
- `main_category_id` → `main_categories.id` (cascade delete)

---

### 5. **SUPPLIERS** (الموردون)
Supplier information for purchases

| Column | Type | Constraints | Notes |
|--------|------|-----------|-------|
| id | bigint, unsigned, auto-increment | PRIMARY KEY | |
| name | string | | Supplier name |
| name_ar | string | | اسم الموزع |
| phone | string | nullable | |
| email | string | nullable | |
| address | text | nullable | |
| address_ar | text | nullable | |
| notes | text | nullable | |
| is_active | boolean | default true | Active/inactive flag |
| timestamps | | | |

**Relationships:**
- Has Many: Products
- Has Many: Purchases

---

### 6. **PRODUCTS** (المنتجات)
Product catalog with inventory and pricing

| Column | Type | Constraints | Notes |
|--------|------|-----------|-------|
| id | bigint, unsigned, auto-increment | PRIMARY KEY | |
| name | string | | Product name (en) |
| name_ar | string | | Product name (ar) |
| sku | string, unique | UNIQUE | Stock Keeping Unit - كود المنتج |
| main_category_id | bigint, unsigned | FK, cascade delete | |
| sub_category_id | bigint, unsigned | FK, cascade delete | |
| purchase_price_per_kg | decimal(12,3) | | سعر الشراء للكيلو |
| selling_price_per_kg | decimal(12,3) | | سعر البيع للكيلو |
| current_stock_kg | decimal(12,3) | | Current inventory in kg |
| minimum_stock_alert | decimal(12,3) | | Alert threshold |
| supplier_id | bigint, unsigned | FK, nullable, set null | Primary supplier |
| notes | text | nullable | |
| created_by | bigint, unsigned | FK, cascade delete | User who created product |
| deleted_at | timestamp | nullable, for soft delete | |
| timestamps | | | |

**Indexes:**
- `INDEX(sku)` - Fast SKU lookup
- `INDEX(main_category_id)` - Category filtering
- `INDEX(supplier_id)` - Supplier filtering

**Relationships:**
- Belongs To: MainCategory, SubCategory, Supplier, User (created_by)
- Has Many: InvoiceItems, PurchaseItems, StockMovements

**Foreign Keys:**
- `main_category_id` → `main_categories.id` (cascade delete)
- `sub_category_id` → `sub_categories.id` (cascade delete)
- `supplier_id` → `suppliers.id` (nullable, set null)
- `created_by` → `users.id` (cascade delete)

**Soft Deletes:** ✓ Enabled

---

### 7. **CLIENTS** (العملاء)
Customer information with credit tracking

| Column | Type | Constraints | Notes |
|--------|------|-----------|-------|
| id | bigint, unsigned, auto-increment | PRIMARY KEY | |
| name | string | | Client name (en) |
| name_ar | string | | Client name (ar) |
| phone | string | nullable | |
| phone_2 | string | nullable | Alternate phone |
| address | text | nullable | |
| address_ar | text | nullable | |
| credit_limit | decimal(14,3) | default 0 | Maximum credit allowed |
| total_debt | decimal(14,3) | default 0 | Current total debt |
| notes | text | nullable | |
| is_active | boolean | default true | Active/inactive flag |
| created_by | bigint, unsigned | FK, cascade delete | |
| deleted_at | timestamp | nullable, for soft delete | |
| timestamps | | | |

**Relationships:**
- Has Many: Invoices
- Belongs To: User (created_by)

**Foreign Keys:**
- `created_by` → `users.id` (cascade delete)

**Soft Deletes:** ✓ Enabled

---

### 8. **INVOICES** (الفواتير)
Sales invoices (regular and quick sales)

| Column | Type | Constraints | Notes |
|--------|------|-----------|-------|
| id | bigint, unsigned, auto-increment | PRIMARY KEY | |
| invoice_number | string, unique | UNIQUE | Auto-generated: INV-YYYYMMDD-XXXX |
| type | enum | regular, quick | Regular (with client) or Quick (walk-in) |
| client_id | bigint, unsigned | FK, nullable, set null | NULL for quick sales |
| user_id | bigint, unsigned | FK, cascade delete | Seller/cashier |
| subtotal | decimal(14,3) | | Before discount/tax |
| discount | decimal(14,3) | default 0 | | خصم
| tax | decimal(14,3) | default 0 | | ضريبة
| total | decimal(14,3) | | After discount + tax |
| amount_paid | decimal(14,3) | default 0 | | المبلغ المدفوع
| remaining_balance | decimal(14,3) | | | الرصيد
| status | enum | paid, partial, unpaid | Payment status |
| invoice_date | date | | |
| notes | text | nullable | |
| deleted_at | timestamp | nullable, for soft delete | |
| timestamps | | | |

**Enums:**
- `type`: 'regular', 'quick'
- `status`: 'paid', 'partial', 'unpaid'

**Indexes:**
- `UNIQUE(invoice_number)` - Fast invoice lookup
- `INDEX(client_id)` - Client filtering
- `INDEX(invoice_date)` - Date range queries
- `INDEX(status)` - Payment status filtering

**Relationships:**
- Belongs To: Client (nullable), User
- Has Many: InvoiceItems, InvoicePayments

**Foreign Keys:**
- `client_id` → `clients.id` (nullable, set null)
- `user_id` → `users.id` (cascade delete)

**Soft Deletes:** ✓ Enabled

---

### 9. **INVOICE_ITEMS** (عناصر الفاتورة)
Line items for each invoice

| Column | Type | Constraints | Notes |
|--------|------|-----------|-------|
| id | bigint, unsigned, auto-increment | PRIMARY KEY | |
| invoice_id | bigint, unsigned | FK, cascade delete | |
| product_id | bigint, unsigned | FK, cascade delete | |
| quantity_kg | decimal(12,3) | | Quantity sold in kg |
| unit_price | decimal(12,3) | | Price per kg at time of sale |
| total | decimal(14,3) | | quantity_kg × unit_price |
| timestamps | | | |

**Indexes:**
- `INDEX(invoice_id)` - Fast invoice item lookup
- `INDEX(product_id)` - Product usage tracking

**Relationships:**
- Belongs To: Invoice, Product

**Foreign Keys:**
- `invoice_id` → `invoices.id` (cascade delete)
- `product_id` → `products.id` (cascade delete)

---

### 10. **INVOICE_PAYMENTS** (دفعات الفاتورة)
Payment tracking for invoices

| Column | Type | Constraints | Notes |
|--------|------|-----------|-------|
| id | bigint, unsigned, auto-increment | PRIMARY KEY | |
| invoice_id | bigint, unsigned | FK, cascade delete | |
| amount | decimal(14,3) | | Payment amount |
| payment_date | date | | |
| payment_method | enum | cash, check, transfer, other | |
| notes | text | nullable | |
| created_by | bigint, unsigned | FK, cascade delete | |
| timestamps | | | |

**Enums:**
- `payment_method`: 'cash', 'check', 'transfer', 'other'

**Indexes:**
- `INDEX(invoice_id)` - Payment lookup
- `INDEX(payment_date)` - Date filtering

**Relationships:**
- Belongs To: Invoice, User (created_by)

**Foreign Keys:**
- `invoice_id` → `invoices.id` (cascade delete)
- `created_by` → `users.id` (cascade delete)

---

### 11. **PURCHASES** (الطلبات من الموردين)
Purchase orders from suppliers

| Column | Type | Constraints | Notes |
|--------|------|-----------|-------|
| id | bigint, unsigned, auto-increment | PRIMARY KEY | |
| purchase_number | string, unique | UNIQUE | Auto-generated: PUR-YYYYMMDD-XXXX |
| supplier_id | bigint, unsigned | FK, cascade delete | |
| user_id | bigint, unsigned | FK, cascade delete | Purchase manager |
| subtotal | decimal(14,3) | | |
| tax | decimal(14,3) | default 0 | |
| total_cost | decimal(14,3) | | |
| purchase_date | date | | |
| status | enum | pending, received, partial | |
| notes | text | nullable | |
| deleted_at | timestamp | nullable, for soft delete | |
| timestamps | | | |

**Enums:**
- `status`: 'pending', 'received', 'partial'

**Indexes:**
- `INDEX(supplier_id)` - Supplier purchases
- `INDEX(purchase_date)` - Date filtering

**Relationships:**
- Belongs To: Supplier, User
- Has Many: PurchaseItems

**Foreign Keys:**
- `supplier_id` → `suppliers.id` (cascade delete)
- `user_id` → `users.id` (cascade delete)

**Soft Deletes:** ✓ Enabled

---

### 12. **PURCHASE_ITEMS** (عناصر الطلب)
Line items for purchase orders

| Column | Type | Constraints | Notes |
|--------|------|-----------|-------|
| id | bigint, unsigned, auto-increment | PRIMARY KEY | |
| purchase_id | bigint, unsigned | FK, cascade delete | |
| product_id | bigint, unsigned | FK, cascade delete | |
| quantity_kg | decimal(12,3) | | Quantity ordered in kg |
| cost_per_kg | decimal(12,3) | | Cost per kg at purchase time |
| total_cost | decimal(14,3) | | quantity_kg × cost_per_kg |
| timestamps | | | |

**Indexes:**
- `INDEX(purchase_id)` - Purchase lookup
- `INDEX(product_id)` - Product tracking

**Relationships:**
- Belongs To: Purchase, Product

**Foreign Keys:**
- `purchase_id` → `purchases.id` (cascade delete)
- `product_id` → `products.id` (cascade delete)

---

### 13. **STOCK_MOVEMENTS** (حركة المخزون)
Audit trail for all inventory movements

| Column | Type | Constraints | Notes |
|--------|------|-----------|-------|
| id | bigint, unsigned, auto-increment | PRIMARY KEY | |
| product_id | bigint, unsigned | FK, cascade delete | |
| type | enum | in, out | Stock in or out |
| quantity_kg | decimal(12,3) | | Absolute value, sign determined by type |
| reference_type | string | nullable | invoice, purchase, adjustment |
| reference_id | bigint, unsigned | nullable | ID of related record |
| notes | text | nullable | |
| created_by | bigint, unsigned | FK, cascade delete | |
| timestamps | | | Entry timestamp = movement time |

**Enums:**
- `type`: 'in', 'out'

**Indexes:**
- `INDEX(product_id, created_at)` - Product movement history
- `INDEX(type)` - Movement type filtering

**Relationships:**
- Belongs To: Product, User (created_by)

**Foreign Keys:**
- `product_id` → `products.id` (cascade delete)
- `created_by` → `users.id` (cascade delete)

**Purpose:** Complete audit trail of all stock movements for reconciliation and reporting

---

### 14. **EXPENSE_CATEGORIES** (تصنيفات المصروفات)
Categories for tracking expenses

| Column | Type | Constraints | Notes |
|--------|------|-----------|-------|
| id | bigint, unsigned, auto-increment | PRIMARY KEY | |
| name | string, unique | UNIQUE | Transportation, Roasting, etc. |
| name_ar | string, unique | UNIQUE | نقل, تحميص, تعبئة, إيجار, كهرباء |
| description | text | nullable | |
| timestamps | | | |

**Relationships:**
- Has Many: Expenses

---

### 15. **EXPENSES** (المصروفات)
Operating expenses tracking

| Column | Type | Constraints | Notes |
|--------|------|-----------|-------|
| id | bigint, unsigned, auto-increment | PRIMARY KEY | |
| expense_category_id | bigint, unsigned | FK, cascade delete | |
| amount | decimal(12,3) | | Expense amount |
| expense_date | date | | Date of expense |
| description | text | nullable | |
| description_ar | text | nullable | |
| reference | string | nullable | Document reference number |
| created_by | bigint, unsigned | FK, cascade delete | |
| timestamps | | | |

**Indexes:**
- `INDEX(expense_date)` - Date filtering
- `INDEX(expense_category_id)` - Category filtering

**Relationships:**
- Belongs To: ExpenseCategory, User (created_by)

**Foreign Keys:**
- `expense_category_id` → `expense_categories.id` (cascade delete)
- `created_by` → `users.id` (cascade delete)

---

## Entity Relationship Diagram (ERD)

```
┌─────────────┐
│   ROLES     │
│  (Roles)    │
└──────┬──────┘
       │ 1:N
       │
┌──────▼──────────────┐
│     USERS           │
│  (مستخدمين)         │
├─────────────────────┤
│ role_id (FK)        │
└──────┬──┬┬──┬──┬────┘
       │ │└─┼──┼──┼──────────────────────┐
       │ │  │  │  │                      │
    ┌──┴─┴──┴──┴──┘                      │
    │                                  │
    │ 1:N                              │ 1:N
    │                                  │
┌───▼─────────────────┐         ┌──────▼──────────────────────┐
│   MAIN_CATEGORIES   │         │      INVOICES               │
│ (التصنيفات الرئيسية)  │         │     (الفواتير)             │
└───┬─────────────────┘         ├─────────────────────────────┤
    │ 1:N                       │ client_id (FK, nullable)    │
    │                           │ user_id (FK)                │
    │                           └──────┬──┬──────────────────┘
┌───▼──────────────────┐              │  │ 1:N
│  SUB_CATEGORIES      │              │  │
│(التصنيفات الفرعية)   │              │  │
│                      │              │  │
└────┬────────────────┘              │  │
     │                               │  │ 1:N
     │ 1:N            ┌──────────────┘  │
     │                │                 │
     │    ┌───────────▼──────────┐      │
     │    │     PRODUCTS         │      │
     │    │    (المنتجات)        │      │
     │    │──────────────────────┤      │
     │    │ supplier_id (FK, null)      │
     │    │ created_by (FK)      │      │
     │    └───┬──────────────────┘      │
     │        │ 1:N                     │
     │        │        ┌────────────────┘
     │        │        │
     │        │   ┌────▼──────────────┐
     │        │   │  INVOICE_ITEMS    │
     │        │   │ (عناصر الفاتورة)   │
     │        └───┤ product_id (FK)   │
     │            │ invoice_id (FK)   │
     │            └───────────────────┘
     │
     │ 1:N
     │
┌────▼──────────────┐
│   SUPPLIERS       │
│    (الموردون)     │
└────┬──────────────┘
     │ 1:N           ┌──────────────────────┐
     │               │  PURCHASES           │
     │               │ (الطلبات من الموردين) │
     │               │──────────────────────┤
     │               │ supplier_id (FK)     │
     │               │ user_id (FK)         │
     │               └────┬────────────────┘
     │                    │ 1:N
     │                    │
     │            ┌───────▼────────────────┐
     │            │  PURCHASE_ITEMS        │
     │            │ (عناصر الطلب)           │
     │            │────────────────────────┤
     │            │ purchase_id (FK)       │
     └────────────┤ product_id (FK)       │
                  └────────────────────────┘


┌─────────────────────┐
│    CLIENTS          │
│    (العملاء)        │
│─────────────────────┤
│ created_by (FK)     │
└────┬────────────────┘
     │ 1:N
     └────────┬─────────────────────────────┐
              │ Referenced in INVOICES       │
              │ (for regular sales)         │
              └─────────────────────────────┘


┌─────────────────────┐
│  STOCK_MOVEMENTS    │
│ (حركة المخزون)      │
│─────────────────────┤
│ product_id (FK)     │
│ created_by (FK)     │
└─────────────────────┘
     Audit trail for all inventory changes


┌──────────────────────────┐
│  EXPENSE_CATEGORIES      │
│ (تصنيفات المصروفات)      │
└────┬─────────────────────┘
     │ 1:N
     │
┌────▼────────────────┐
│    EXPENSES         │
│   (المصروفات)       │
│────────────────────┤
│ expense_category_id│
│ created_by (FK)    │
└────────────────────┘


┌──────────────────────────┐
│   INVOICE_PAYMENTS       │
│  (دفعات الفاتورة)        │
│──────────────────────────┤
│ invoice_id (FK)          │
│ created_by (FK)          │
└──────────────────────────┘
     Payment tracking for invoices
```

---

## Key Design Decisions

### 1. **Foreign Key Strategy**
- **Cascade Delete:** Applied to all relationships where deleting parent should remove children
  - Users → Products/Invoices/etc.
  - MainCategory → SubCategories & Products
  - Invoices → InvoiceItems
  - Purchases → PurchaseItems

- **Set Null:** Applied to optional relationships
  - Clients in Invoices (for quick sales)
  - Suppliers in Products (optional; no primary supplier)
  - Role in Users (optional; user might not have role assigned)

### 2. **Soft Deletes**
Used for critical business entities to maintain audit trails:
- **Products** - Preserve historical sales records tied to deleted products
- **Clients** - Keep client history and invoices accessible
- **Invoices** - Maintain financial records even if cancelled
- **Purchases** - Preserve purchase history

NOT used for:
- Stock Movements, InvoiceItems, PurchaseItems (audit trail already serves this)
- Payments, Expenses, Categories (business logic constraints)

### 3. **Indexing Strategy**
- **Unique Indexes:** SKU, Invoice Number, Purchase Number, Role Name (prevent duplicates)
- **Foreign Key Indexes:** Automatic in MySQL 8.0+
- **Filter Indexes:** Columns frequently used in WHERE clauses
  - invoice_date, expense_date (date filtering)
  - status (payment status queries)
- **Composite Indexes:** (product_id, created_at) for time-based stock movement queries

### 4. **Decimal Precision**
Used `decimal(12,3)` and `decimal(14,3)` for all monetary/weight values:
- Supports up to 1 billion units with 3 decimal places
- Avoids floating-point precision issues in financial calculations

### 5. **Bilingual Fields**
All user-facing text has both English and Arabic versions:
- `name` (English) + `name_ar` (Arabic)
- `description` + `description_ar`
- Enables seamless RTL UI in Arabic

### 6. **Enum Types**
Ensures data integrity for fixed-set values:
- Invoice type: regular/quick
- Invoice status: paid/partial/unpaid
- Purchase status: pending/received/partial
- Stock type: in/out
- Payment method: cash/check/transfer/other

### 7. **Audit Trail**
**Stock Movements Table** serves as complete audit log:
- Records every inventory change (in/out)
- Links to source (invoice/purchase/adjustment)
- Tracks who made the change and when
- Essential for reconciliation and reports

### 8. **No Direct Role Assignment to Permissions**
Current design uses simple roles (Admin/Sales/Accountant) without permission matrix.
Can be extended later with `roles_permissions` and `permissions` tables if needed.

---

## Database Constraints Summary

| Constraint Type | Count | Examples |
|-----------------|-------|----------|
| Foreign Keys | 17 | product.supplier_id → suppliers.id |
| Unique Constraints | 7 | products.sku, invoices.invoice_number |
| Not Null Constraints | 30+ | All core business fields |
| Check Constraints | Via Enums | 8 enum fields |
| Default Values | 12 | boolean flags, decimals = 0 |
| Soft Deletes | 5 tables | products, clients, invoices, purchases |

---

## Migration Execution Order

The migrations are automatically ordered by timestamp:

1. `create_roles_table` - Base reference table
2. `create_main_categories_table` - Supports products
3. `create_sub_categories_table` - Depends on main_categories
4. `create_suppliers_table` - Independent
5. `create_products_table` - Depends on categories & suppliers
6. `add_role_to_users_table` - Extends existing users table
7. `create_clients_table` - Independent
8. `create_invoices_table` - Depends on clients & users
9. `create_invoice_items_table` - Depends on invoices & products
10. `create_invoice_payments_table` - Depends on invoices & users
11. `create_purchases_table` - Depends on suppliers & users
12. `create_purchase_items_table` - Depends on purchases & products
13. `create_expense_categories_table` - Independent
14. `create_expenses_table` - Depends on expense_categories & users
15. `create_stock_movements_table` - Depends on products & users

---

## Performance Considerations

| Resource | Optimization | Implementation |
|----------|--------------|-----------------|
| Query Speed | Indexing | Composite indexes on (product_id, created_at), single indexes on foreign keys |
| Reporting | Denormalization | current_stock_kg stored in products table for fast dashboard queries |
| Transactions | Atomicity | Database transactions for invoice + stock deduction |
| Archival | Soft Deletes | 5 tables support soft delete for historical queries |
| Audit | Stock Movements | Complete trail of inventory changes |

---

## Related Files

- **Models:** `/app/Models/` - 15 Eloquent models with relationships
- **Migrations:** `/database/migrations/` - 15 migration files
- **Factories:** `/database/factories/` - For seeding test data
- **Seeds:** `/database/seeders/` - Initialize roles and categories

---

**Last Updated:** February 19, 2026
**Database Version:** MySQL 8.0+
**Laravel Version:** 12.0+
