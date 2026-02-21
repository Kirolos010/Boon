# PHASE 5 & 6: FORM REQUESTS & CONTROLLERS
**Status**: ✅ **COMPLETE**  
**Date**: February 19, 2026  
**Lines of Code**: 1,500+  

---

## PHASE 5: FORM REQUESTS (5 Files Created)

### Overview
Five comprehensive Form Request classes with complete Arabic validation messages for input validation and error handling.

---

### 1. **StoreProductRequest** 
`app/Http/Requests/StoreProductRequest.php`

**Authorization**: Admin or Sales users only

**Validation Rules**:
```php
[
    'name' => 'required|string|max:255',
    'name_ar' => 'required|string|max:255',
    'sku' => 'required|string|max:100|unique:products,sku',
    'main_category_id' => 'required|exists:main_categories,id',
    'sub_category_id' => 'required|exists:sub_categories,id',
    'supplier_id' => 'nullable|exists:suppliers,id',
    'purchase_price_per_kg' => 'required|numeric|min:0',
    'selling_price_per_kg' => 'required|numeric|min:0',
    'minimum_stock_alert' => 'required|numeric|min:0',
    'current_stock_kg' => 'nullable|numeric|min:0',
    'notes' => 'nullable|string|max:1000',
]
```

**Arabic Error Messages** (23 messages):
- ✓ Required field validations
- ✓ Data type validations
- ✓ Length constraints
- ✓ Unique constraint for SKU
- ✓ Relationship existence checks

---

### 2. **StoreInvoiceRequest**
`app/Http/Requests/StoreInvoiceRequest.php`

**Authorization**: Admin or Sales users only

**Validation Rules**:
```php
[
    'client_id' => 'nullable|exists:clients,id',
    'items' => 'required|array|min:1',
    'items.*.product_id' => 'required|exists:products,id',
    'items.*.quantity_kg' => 'required|numeric|min:0.01',
    'discount' => 'nullable|numeric|min:0',
    'tax' => 'nullable|numeric|min:0',
    'amount_paid' => 'nullable|numeric|min:0',
    'payment_method' => 'nullable|in:cash,check,transfer,other',
    'notes' => 'nullable|string|max:1000',
]
```

**Features**:
- Validates at least 1 item required
- Validates each item (product exists, quantity > 0)
- Optional client (for account sales)
- Optional discount and tax
- Optional payment method (defaults to unpaid)
- Support for partial payments

---

### 3. **StoreQuickSaleRequest**
`app/Http/Requests/StoreQuickSaleRequest.php`

**Authorization**: Admin or Sales users only

**Validation Rules** (similar to invoice but no client):
```php
[
    'items' => 'required|array|min:1',
    'items.*.product_id' => 'required|exists:products,id',
    'items.*.quantity_kg' => 'required|numeric|min:0.01',
    'discount' => 'nullable|numeric|min:0',
    'tax' => 'nullable|numeric|min:0',
    'payment_method' => 'required|in:cash,check,transfer,other',
    'notes' => 'nullable|string|max:1000',
]
```

**Special Features**:
- No client selection (walk-in sale)
- Payment method required
- Automatically marked as "quick" and "paid" in service layer
- Used for immediate cash sales

---

### 4. **StorePurchaseRequest**
`app/Http/Requests/StorePurchaseRequest.php`

**Authorization**: Admin or Sales users only

**Validation Rules**:
```php
[
    'supplier_id' => 'required|exists:suppliers,id',
    'items' => 'required|array|min:1',
    'items.*.product_id' => 'required|exists:products,id',
    'items.*.quantity_kg' => 'required|numeric|min:0.01',
    'items.*.unit_price' => 'required|numeric|min:0',
    'tax' => 'nullable|numeric|min:0',
    'notes' => 'nullable|string|max:1000',
]
```

**Features**:
- Validates supplier exists
- Each item must have: product, quantity, per-unit price
- Optional tax on entire purchase
- Purchase status defaults to "pending" until received

---

### 5. **StoreClientRequest**
`app/Http/Requests/StoreClientRequest.php`

**Authorization**: Admin or Sales users only

**Validation Rules**:
```php
[
    'name' => 'required|string|max:255',
    'name_ar' => 'required|string|max:255',
    'phone' => 'required|string|max:20',
    'email' => 'nullable|email|max:255|unique:clients,email',
    'address' => 'nullable|string|max:500',
    'credit_limit' => 'nullable|numeric|min:0',
    'notes' => 'nullable|string|max:1000',
]
```

**Features**:
- Bilingual name (English + Arabic)
- Phone number required and validated
- Email optional but unique if provided
- Address optional
- Credit limit optional (default 0)
- Notes for additional customer info

---

## PHASE 6: RESOURCE CONTROLLERS (7 Controllers Created)

### Overview
Seven thin, service-oriented controllers that delegate all business logic to services. Controllers handle HTTP request/response mapping only.

---

### 1. **ProductController**
`app/Http/Controllers/ProductController.php`

**Service Injected**: `ProductService`

**Methods**:

| Method | Route | Logic |
|--------|-------|-------|
| `index()` | `GET /products` | Lists all products with stock |
| `create()` | `GET /products/create` | Shows form view |
| `store()` | `POST /products` | Creates product via service |
| `show($id)` | `GET /products/{id}` | Gets product details with profit info |
| `edit($id)` | `GET /products/{id}/edit` | Shows edit form |
| `update()` | `PUT /products/{id}` | Updates product via service |
| `destroy($id)` | `DELETE /products/{id}` | Deletes product permanently |
| `lowStock()` | `GET /products/low-stock` | Lists products below alert level |
| `adjustStock()` | `POST /products/{id}/adjust` | Manual stock adjustment |

**Response Format** (all endpoints):
```json
{
  "status": "success|error",
  "message": "Arabic message",
  "data": { ... }
}
```

---

### 2. **ClientController**
`app/Http/Controllers/ClientController.php`

**Methods**:

| Method | Route | Logic |
|--------|-------|-------|
| `index()` | `GET /clients` | Paginated client list (15 per page) |
| `create()` | `GET /clients/create` | Shows form view |
| `store()` | `POST /clients` | Creates client |
| `show($id)` | `GET /clients/{id}` | Gets client + invoice history |
| `edit($id)` | `GET /clients/{id}/edit` | Shows edit form |
| `update()` | `PUT /clients/{id}` | Updates client info |
| `destroy($id)` | `DELETE /clients/{id}` | Soft deletes client |
| `invoices($id)` | `GET /clients/{id}/invoices` | Lists client's invoices |

**Features**:
- Automatic pagination
- Soft deletes preserved
- Shows total debt and credit status
- Tracks payment history per client

---

### 3. **InvoiceController**
`app/Http/Controllers/InvoiceController.php`

**Service Injected**: `InvoiceService`

**Methods**:

| Method | Route | Logic |
|--------|-------|-------|
| `index()` | `GET /invoices` | Lists regular invoices (not quick sales) |
| `create()` | `GET /invoices/create` | Shows form view |
| `store()` | `POST /invoices` | Creates invoice via service (DB transaction) |
| `show($id)` | `GET /invoices/{id}` | Gets full invoice with profit calc |
| `edit($id)` | `GET /invoices/{id}/edit` | Shows edit form |
| `update()` | `PUT /invoices/{id}` | Updates invoice items & amounts |
| `destroy($id)` | `DELETE /invoices/{id}` | Cancels invoice, restores stock |
| `recordPayment()` | `POST /invoices/{id}/payment` | Records partial/full payment |

**Delegations to InvoiceService**:
- `createInvoice()` - Full creation with stock deduction
- `updateInvoice()` - Update with stock restoration
- `cancelInvoice()` - Soft delete with stock restore
- `recordPayment()` - Payment tracking
- `getInvoiceDetails()` - Full invoice info

---

### 4. **QuickSaleController**
`app/Http/Controllers/QuickSaleController.php`

**Service Injected**: `InvoiceService` (uses `createQuickSale()`)

**Methods**:

| Method | Route | Logic |
|--------|-------|-------|
| `index()` | `GET /quick-sales` | Lists all quick sales |
| `create()` | `GET /quick-sales/create` | Shows form view |
| `store()` | `POST /quick-sales` | Creates quick sale (auto-paid) |
| `show($id)` | `GET /quick-sales/{id}` | Gets sale details |
| `destroy($id)` | `DELETE /quick-sales/{id}` | Cancels quick sale |

**Differences from Regular Invoices**:
- No client selection
- Auto-marked as "paid" / type "quick"
- Payment method required
- Faster checkout flow

---

### 5. **PurchaseController**
`app/Http/Controllers/PurchaseController.php`

**Service Injected**: `PurchaseService`

**Methods**:

| Method | Route | Logic |
|--------|-------|-------|
| `index()` | `GET /purchases` | Lists all purchase orders |
| `create()` | `GET /purchases/create` | Shows form view |
| `store()` | `POST /purchases` | Creates purchase (status='pending') |
| `show($id)` | `GET /purchases/{id}` | Gets PO with profit projection |
| `edit($id)` | `GET /purchases/{id}/edit` | Shows edit form |
| `update()` | `PUT /purchases/{id}` | Updates PO items/prices |
| `destroy($id)` | `DELETE /purchases/{id}` | Cancels PO (restores stock if received) |
| `receive()` | `POST /purchases/{id}/receive` | Receives items, adds stock |

**Delegations to PurchaseService**:
- `createPurchase()` - Creates PO with DB transaction
- `receivePurchase()` - Processes receipt, updates stock
- `cancelPurchase()` - Cancels with stock reversal
- `getPurchaseDetails()` - Full order info + profit calc

---

### 6. **ExpenseController**
`app/Http/Controllers/ExpenseController.php`

**Methods**:

| Method | Route | Logic |
|--------|-------|-------|
| `index()` | `GET /expenses` | Lists all expenses |
| `create()` | `GET /expenses/create` | Shows form with categories |
| `store()` | `POST /expenses` | Creates expense, auto-assigns user |
| `show($id)` | `GET /expenses/{id}` | Gets expense details |
| `edit($id)` | `GET /expenses/{id}/edit` | Shows edit form |
| `update()` | `PUT /expenses/{id}` | Updates expense data |
| `destroy($id)` | `DELETE /expenses/{id}` | Deletes expense |

**Features**:
- Auto-populates current user (created_by)
- Validates expense category exists
- Requires date and amount
- Tracks expenses for profit reports
- Supports multiple categories (rent, utilities, wages, etc.)

---

### 7. **DashboardController**
`app/Http/Controllers/DashboardController.php`

**Services Injected**: `ReportService`, `InvoiceService`, `ProductService`

**Methods**:

| Method | Route | Purpose |
|--------|-------|---------|
| `index()` | `GET /dashboard` | Main dashboard with all KPIs |
| `salesReport()` | `GET /dashboard/sales` | Detailed sales breakdown |
| `profitReport()` | `GET /dashboard/profit` | Gross vs net profit |
| `inventoryReport()` | `GET /dashboard/inventory` | Stock valuation & status |

**Dashboard Index Response** (single endpoint - all data):
```json
{
  "status": "success",
  "data": {
    "today_summary": { 
      "total_invoices": 5,
      "total_sales": 2500.00,
      "total_profit": 450.00,
      "total_paid": 2000.00,
      "total_pending": 500.00
    },
    "daily_report": { ... },
    "monthly_trend": [ ... ],
    "sales_by_product": [ ... ],
    "top_products": [ ... ],
    "top_clients": [ ... ],
    "low_stock_products": [ ... ],
    "inventory_summary": { ... },
    "profit_report": { ... },
    "stats": {
      "total_invoices": 150,
      "quick_sales": 45,
      "total_clients": 32,
      "total_products": 120,
      "pending_purchases": 8,
      "pending_payments": 12
    }
  }
}
```

**Key Dashboard Data**:
1. **Today's Summary** - Real-time daily metrics
2. **Daily Report** - Quick vs regular sales breakdown
3. **Monthly Trend** - 12-month revenue & profit graph
4. **Sales by Product** - Top products by revenue
5. **Top Products** - Top 5 best sellers
6. **Top Clients** - Top 5 clients by amount
7. **Low Stock Products** - Alert for re-ordering
8. **Inventory Summary** - Total value and status
9. **Profit Report** - Gross - Expenses = Net
10. **Quick Stats** - Key numbers at a glance

---

## ARCHITECTURE PATTERN: THIN CONTROLLERS

### Controller Structure
```php
// 1. Inject services via constructor
public function __construct(protected ProductService $productService)
{
}

// 2. Validate input (via Form Request, not controller)
public function store(StoreProductRequest $request)
{
    // 3. Call service method with validated data
    $product = $this->productService->createProduct($request->validated());
    
    // 4. Return JSON response with status
    return response()->json([
        'status' => 'success',
        'message' => 'تم إنشاء المنتج بنجاح',
        'data' => $product,
    ], 201);
}
```

### Key Principles
✓ **Single Responsibility**: Each controller handles ONE resource  
✓ **Service Delegation**: All business logic in services  
✓ **Form Requests**: Validation in request classes  
✓ **Error Handling**: Try-catch with Arabic messages  
✓ **Response Consistency**: Standardized JSON responses  
✓ **Route Model Binding**: Simplified parameter retrieval  

---

## INPUT VALIDATION FLOW

```
HTTP Request
    ↓
Form Request (StoreProductRequest)
    ↓
- Returns validated array if pass
- Returns 422 with errors if fail
    ↓
Controller receives validated data
    ↓
Calls Service Method
    ↓
Service layer handles business logic
    ↓
Controller returns JSON response
```

---

## ERROR HANDLING

### All Controllers Use
```php
try {
    $product = $this->productService->createProduct($request->validated());
    return response()->json([...success...], 201);
} catch (\Exception $e) {
    return response()->json([
        'status' => 'error',
        'message' => $e->getMessage(), // Arabic message from service
    ], 400);
}
```

### Messages are Arabic
- `'تم إنشاء المنتج بنجاح'` - Product created successfully
- `'تم تحديث الفاتورة بنجاح'` - Invoice updated successfully  
- `'تم إلغاء عملية البيع بنجاح'` - Sale cancelled successfully
- `'كود المنتج موجود بالفعل'` - SKU already exists

---

## AUTHORIZATION

### All Controllers Check Role
```php
public function authorize(): bool
{
    return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSales());
}
```

### Role-Based Access
| Controller | Admin | Sales | Accountant |
|------------|:-----:|:-----:|:----------:|
| Products | ✓ | ✓ | ✗ |
| Clients | ✓ | ✓ | ✗ |
| Invoices | ✓ | ✓ | ✓ (view) |
| QuickSale | ✓ | ✓ | ✗ |
| Purchases | ✓ | ✓ | ✗ |
| Expenses | ✓ | ✗ | ✓ |
| Dashboard | ✓ | ✓ | ✓ |

---

## RESPONSE CODES

| Code | Meaning | Example |
|------|---------|---------|
| 200 | GET success | List products |
| 201 | POST success | Create invoice |
| 400 | Validation error | Invalid quantity |
| 404 | Not found | Non-existent product |
| 422 | Validation failed | Missing required field |
| 500 | Server error | Database error |

---

## PAGINATION

### All List Endpoints Use
```php
// ProductController@index
$products = Product::paginate(15);

// ClientController@index
$clients = Client::paginate(15);

// InvoiceController@invoices
$invoices = $client->invoices()->paginate(10);
```

### Response Format
```json
{
  "data": [...],
  "links": {...},
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 72
  }
}
```

---

## FILE SUMMARY

**Form Requests** (5 files, ~400 lines):
- ✓ StoreProductRequest (23 Arabic messages)
- ✓ StoreInvoiceRequest (19 Arabic messages)
- ✓ StoreQuickSaleRequest (17 Arabic messages)
- ✓ StorePurchaseRequest (18 Arabic messages)
- ✓ StoreClientRequest (17 Arabic messages)

**Controllers** (7 files, ~1100 lines):
- ✓ ProductController (9 methods)
- ✓ ClientController (8 methods)
- ✓ InvoiceController (8 methods)
- ✓ QuickSaleController (5 methods)
- ✓ PurchaseController (8 methods)
- ✓ ExpenseController (7 methods)
- ✓ DashboardController (4 methods)

**Total**: 12 files, ~1500 lines of code

---

## NEXT PHASE: PHASE 7 - DASHBOARD & BLADE UI

**Pending Tasks**:
- Create RTL Blade layout template
- Build dashboard views with cards & charts
- Create reusable Blade components
- Implement form views (create/edit for all resources)
- Add pagination partial
- Coffee-themed styling & colors
- Arabic translations in all templates

**Status**: Ready for PHASE 7 confirmation

---

**Phase Status**: ✅ COMPLETE - AWAITING PHASE 7 CONFIRMATION
