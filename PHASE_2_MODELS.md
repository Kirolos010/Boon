# PHASE 2: MODELS & RELATIONSHIPS - COMPLETE ✅

## Created Eloquent Models (15 total)

All models are built with:
- ✅ Proper relationships
- ✅ Fillable array protection
- ✅ Type casting
- ✅ Query scopes
- ✅ Helper methods
- ✅ Soft deletes where needed

---

## Model Relationships Overview

### 1. **Role Model**
```php
// Relationships
- hasMany(User::class) - All users with this role

// Attributes
- name (e.g., 'admin')
- name_ar (e.g., 'مدير')
- description
```

### 2. **User Model** (Extended)
```php
// Relationships
- belongsTo(Role::class) - User's assigned role
- hasMany(Product::class, 'created_by') - Products created by user
- hasMany(Invoice::class) - Invoices created by user
- hasMany(Purchase::class) - Purchases created by user
- hasMany(Expense::class, 'created_by') - Expenses created by user
- hasMany(Client::class, 'created_by') - Clients created by user
- hasMany(StockMovement::class, 'created_by') - Stock movements
- hasMany(InvoicePayment::class, 'created_by') - Invoice payments

// Methods
- hasRole($roleName) - Check if user has role
- isAdmin() - Check if admin
- isSales() - Check if sales person
- isAccountant() - Check if accountant
```

### 3. **MainCategory Model**
```php
// Relationships
- hasMany(SubCategory::class) - Sub categories in this category
- hasMany(Product::class) - Products in this category

// Attributes
- name (e.g., 'Coffee')
- name_ar (e.g., 'بن')
- description
```

### 4. **SubCategory Model**
```php
// Relationships
- belongsTo(MainCategory::class) - Parent category
- hasMany(Product::class) - Products in this sub category

// Attributes
- name (e.g., 'Brazilian')
- name_ar (e.g., 'برازيلي')
```

### 5. **Supplier Model**
```php
// Relationships
- hasMany(Product::class) - Products from this supplier
- hasMany(Purchase::class) - Purchase orders

// Scopes
- active() - Get active suppliers

// Attributes
- name, name_ar
- phone, email
- address, address_ar
- notes
- is_active (boolean)
```

### 6. **Product Model** (Soft Delete enabled)
```php
// Relationships
- belongsTo(MainCategory::class)
- belongsTo(SubCategory::class)
- belongsTo(Supplier::class)
- belongsTo(User::class, 'created_by') - creator()
- hasMany(InvoiceItem::class)
- hasMany(PurchaseItem::class)
- hasMany(StockMovement::class)

// Methods
- isLowStock() - Check if inventory below minimum
- getProfitMarginPercentage() - Calculate profit margin

// Scopes
- byCategory($id)
- bySubCategory($id)
- bySupplier($id)
- lowStock() - Products below minimum alert
- search($term) - Search by name, sku

// Attributes
- sku (UNIQUE)
- purchase_price_per_kg, selling_price_per_kg
- current_stock_kg
- minimum_stock_alert
```

### 7. **Client Model** (Soft Delete enabled)
```php
// Relationships
- belongsTo(User::class, 'created_by') - creator()
- hasMany(Invoice::class) - Client's invoices

// Methods
- hasAvailableCredit($amount) - Check credit availability
- getAvailableCredit() - Get remaining credit
- getTotalPurchases() - Calculate total purchase amount

// Scopes
- active() - Active clients only
- search($term) - Search by name, phone

// Attributes
- credit_limit (decimal)
- total_debt (decimal)
- is_active (boolean)
```

### 8. **Invoice Model** (Soft Delete enabled)
```php
// Relationships
- belongsTo(Client::class, 'client_id') - nullable for quick sales
- belongsTo(User::class) - Creator/cashier
- hasMany(InvoiceItem::class, null, null, 'items')
- hasMany(InvoicePayment::class)

// Constants
- TYPE_REGULAR = 'regular'
- TYPE_QUICK = 'quick'
- STATUS_PAID = 'paid'
- STATUS_PARTIAL = 'partial'
- STATUS_UNPAID = 'unpaid'

// Methods
- isPaid()
- isPartial()
- isUnpaid()
- calculateProfit() - Calculate total profit from invoice

// Scopes
- quickSales()
- regular()
- byClient($id)
- dateBetween($start, $end)
- byStatus($status)
- today()
- thisMonth()

// Attributes
- invoice_number (UNIQUE)
- type (enum: regular/quick)
- status (enum: paid/partial/unpaid)
- subtotal, discount, tax, total
- amount_paid, remaining_balance
```

### 9. **InvoiceItem Model**
```php
// Relationships
- belongsTo(Invoice::class)
- belongsTo(Product::class)

// Methods
- calculateProfit() - Profit for this line item

// Attributes
- quantity_kg, unit_price, total
```

### 10. **Purchase Model** (Soft Delete enabled)
```php
// Relationships
- belongsTo(Supplier::class)
- belongsTo(User::class)
- hasMany(PurchaseItem::class, null, null, 'items')

// Constants
- STATUS_PENDING = 'pending'
- STATUS_RECEIVED = 'received'
- STATUS_PARTIAL = 'partial'

// Methods
- isReceived() - Check if fully received

// Scopes
- bySupplier($id)
- dateBetween($start, $end)
- byStatus($status)
- pending()

// Attributes
- purchase_number (UNIQUE)
- status (enum: pending/received/partial)
- subtotal, tax, total_cost
```

### 11. **PurchaseItem Model**
```php
// Relationships
- belongsTo(Purchase::class)
- belongsTo(Product::class)

// Attributes
- quantity_kg, cost_per_kg, total_cost
```

### 12. **ExpenseCategory Model**
```php
// Relationships
- hasMany(Expense::class)

// Attributes
- name (e.g., 'Transportation')
- name_ar (e.g., 'نقل')
- description
```

### 13. **Expense Model**
```php
// Relationships
- belongsTo(ExpenseCategory::class, 'expense_category_id')
- belongsTo(User::class, 'created_by') - creator()

// Scopes
- byCategory($id)
- dateBetween($start, $end)

// Attributes
- amount (decimal)
- expense_date
- description, description_ar
- reference (e.g., receipt number)
```

### 14. **InvoicePayment Model**
```php
// Relationships
- belongsTo(Invoice::class)
- belongsTo(User::class, 'created_by') - recorder()

// Constants
- METHOD_CASH = 'cash'
- METHOD_CHECK = 'check'
- METHOD_TRANSFER = 'transfer'
- METHOD_OTHER = 'other'

// Scopes
- byInvoice($id)
- byMethod($method)
- dateBetween($start, $end)

// Attributes
- amount (decimal)
- payment_date
- payment_method (enum)
```

### 15. **StockMovement Model**
```php
// Relationships
- belongsTo(Product::class)
- belongsTo(User::class, 'created_by') - creator()

// Constants
- TYPE_IN = 'in' - Stock addition
- TYPE_OUT = 'out' - Stock deduction

// Scopes
- byProduct($id)
- byType($type)
- stockIn() - Only stock in movements
- stockOut() - Only stock out movements
- byReferenceType($type) - Filter by invoice/purchase/adjustment
- byReference($type, $id) - Filter by specific reference

// Attributes
- quantity_kg (always positive, sign determined by type)
- reference_type (invoice/purchase/adjustment)
- reference_id (ID of related record)

// Purpose
Complete audit trail of all inventory changes
```

---

## Relationship Diagram

```
┌────────────────────────────────────────┐
│            USERS (Users)               │
│ with role_id (FK → roles)              │
├────────────────────────────────────────┤
│ 1->N Products (created_by)             │
│ 1->N Invoices                          │
│ 1->N Purchases                         │
│ 1->N Expenses (created_by)             │
│ 1->N Clients (created_by)              │
│ 1->N StockMovements (created_by)       │
│ 1->N InvoicePayments (created_by)      │
└────────────────────────────────────────┘

┌────────────────────────────────────────┐
│       MAIN_CATEGORIES                  │
├────────────────────────────────────────┤
│ 1->N SubCategories                     │
│ 1->N Products                          │
└────────────────────────────────────────┘
         ↓
┌────────────────────────────────────────┐
│      SUB_CATEGORIES                    │
├────────────────────────────────────────┤
│ N->1 MainCategory                      │
│ 1->N Products                          │
└────────────────────────────────────────┘
         ↓
┌────────────────────────────────────────┐
│         PRODUCTS                       │
│ (Soft Delete)                          │
├────────────────────────────────────────┤
│ N->1 MainCategory                      │
│ N->1 SubCategory                       │
│ N->1 Supplier                          │
│ N->1 User (created_by)                 │
│ 1->N InvoiceItems                      │
│ 1->N PurchaseItems                     │
│ 1->N StockMovements                    │
└────────────────────────────────────────┘

┌────────────────────────────────────────┐
│       SUPPLIERS                        │
├────────────────────────────────────────┤
│ 1->N Products                          │
│ 1->N Purchases                         │
└────────────────────────────────────────┘

┌────────────────────────────────────────┐
│        CLIENTS                         │
│ (Soft Delete)                          │
├────────────────────────────────────────┤
│ N->1 User (created_by)                 │
│ 1->N Invoices                          │
└────────────────────────────────────────┘

┌────────────────────────────────────────┐
│        INVOICES                        │
│ (Soft Delete)                          │
│ type: regular/quick                    │
│ status: paid/partial/unpaid            │
├────────────────────────────────────────┤
│ N->1 Client (nullable)                 │
│ N->1 User                              │
│ 1->N InvoiceItems                      │
│ 1->N InvoicePayments                   │
└────────────────────────────────────────┘
         ↓
┌────────────────────────────────────────┐
│      INVOICE_ITEMS                     │
├────────────────────────────────────────┤
│ N->1 Invoice                           │
│ N->1 Product                           │
└────────────────────────────────────────┘

┌────────────────────────────────────────┐
│     INVOICE_PAYMENTS                   │
├────────────────────────────────────────┤
│ N->1 Invoice                           │
│ N->1 User (created_by)                 │
└────────────────────────────────────────┘

┌────────────────────────────────────────┐
│       PURCHASES                        │
│ (Soft Delete)                          │
│ status: pending/received/partial       │
├────────────────────────────────────────┤
│ N->1 Supplier                          │
│ N->1 User                              │
│ 1->N PurchaseItems                     │
└────────────────────────────────────────┘
         ↓
┌────────────────────────────────────────┐
│     PURCHASE_ITEMS                     │
├────────────────────────────────────────┤
│ N->1 Purchase                          │
│ N->1 Product                           │
└────────────────────────────────────────┘

┌────────────────────────────────────────┐
│   EXPENSE_CATEGORIES                   │
├────────────────────────────────────────┤
│ 1->N Expenses                          │
└────────────────────────────────────────┘
         ↓
┌────────────────────────────────────────┐
│        EXPENSES                        │
├────────────────────────────────────────┤
│ N->1 ExpenseCategory                   │
│ N->1 User (created_by)                 │
└────────────────────────────────────────┘

┌────────────────────────────────────────┐
│     STOCK_MOVEMENTS                    │
│ (Audit Trail)                          │
│ type: in/out                           │
├────────────────────────────────────────┤
│ N->1 Product                           │
│ N->1 User (created_by)                 │
│ reference_type: invoice/purchase/adj   │
│ reference_id: ID of origin             │
└────────────────────────────────────────┘
```

---

## Key Features

### ✅ Type Casting
All decimal/date fields automatically cast to correct types:
```php
// Example in Product model
protected $casts = [
    'purchase_price_per_kg' => 'decimal:3',
    'selling_price_per_kg' => 'decimal:3',
    'current_stock_kg' => 'decimal:3',
    'minimum_stock_alert' => 'decimal:3',
];
```

### ✅ Scopes for Filtering
Chainable query scopes for clean controller code:
```php
// Example usage
$lowStockProducts = Product::lowStock()->get();
$todayInvoices = Invoice::today()->byStatus('unpaid')->get();
$supplierPurchases = Purchase::bySupplier($id)->dateBetween($start, $end)->get();
```

### ✅ Helper Methods
Business logic methods for calculations:
```php
// Example usage
$profit = $invoice->calculateProfit();
$margin = $product->getProfitMarginPercentage();
$available = $client->getAvailableCredit();
```

### ✅ Soft Deletes
5 models support soft deletes to preserve audit trails:
- Products
- Clients
- Invoices
- Purchases

### ✅ Constants for Enums
Type-safe constants instead of string literals:
```php
// Instead of: 'status' => 'paid'
// Use: 'status' => Invoice::STATUS_PAID
```

### ✅ Role Checking Methods
Easy role verification in User model:
```php
if ($user->isAdmin()) { /* ... */ }
if ($user->isSales()) { /* ... */ }
if ($user->isAccountant()) { /* ... */ }
```

---

## All Models Location

```
/app/Models/
├── Role.php ✅
├── User.php ✅ (Extended)
├── MainCategory.php ✅
├── SubCategory.php ✅
├── Supplier.php ✅
├── Product.php ✅ (Soft Delete)
├── Client.php ✅ (Soft Delete)
├── Invoice.php ✅ (Soft Delete)
├── InvoiceItem.php ✅
├── Purchase.php ✅ (Soft Delete)
├── PurchaseItem.php ✅
├── ExpenseCategory.php ✅
├── Expense.php ✅
├── InvoicePayment.php ✅
└── StockMovement.php ✅
```

---

## Ready for PHASE 3: ROLE SYSTEM

✅ All models created with:
✅ Complete relationships (16 relationships)
✅ Query scopes (20+ scopes)
✅ Helper methods (10+ methods)
✅ Type casting
✅ Soft deletes where needed
✅ Fillable array protection
✅ Constants for enum values

**Next Phase:** Role System implementation with middleware and seeder

**Date:** February 19, 2026
