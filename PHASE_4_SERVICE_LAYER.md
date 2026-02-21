# PHASE 4: SERVICE LAYER - COMPLETE ✅

## Overview
Complete Service Layer implementation with all business logic encapsulated, following SOLID principles and clean architecture patterns.

---

## Services Created (4 Total)

### 1. **InvoiceService** - Complete Invoice Management
**File:** `app/Services/InvoiceService.php`

#### Core Methods:

**createInvoice(array $data): Invoice**
- Creates invoice with items
- Uses DB transaction for atomicity
- Validates stock availability
- Deducts stock automatically
- Creates stock movement records
- Updates client debt
- Generates unique invoice number
- Calculates invoice status automatically
- Records payments if provided
- Arabic error messages

**Parameters:**
```php
[
    'type' => 'regular' | 'quick',
    'client_id' => int (nullable),
    'invoice_date' => '2026-02-19',
    'discount' => float,
    'amount_paid' => float,
    'payment_method' => 'cash' | 'check' | 'transfer',
    'notes' => string,
    'items' => [
        [
            'product_id' => int,
            'quantity_kg' => float,
            'unit_price' => float (optional, uses product price),
        ],
        ...
    ]
]
```

**Stock Deduction Flow:**
1. Validate stock availability
2. Deduct from product.current_stock_kg
3. Create StockMovement record with reference_type='invoice'
4. Record audit trail

**createQuickSale(array $data): Invoice**
- Quick sale (walk-in customer)
- No client required
- Automatically marked as paid
- Sets type='quick'

**recordPayment(Invoice, float, string): InvoicePayment**
- Records payment
- Updates invoice amounts
- Updates remaining_balance
- Recalculates status
- Updates client debt

**calculateProfit(Invoice): float**
- Calculates total profit from invoice
- Formula: (selling_price - purchase_price) × quantity for each item
- Returns rounded float

**updateInvoice(Invoice, array): Invoice**
- Updates invoice with new items
- Restores old stock
- Deducts new stock
- Recalculates totals

**cancelInvoice(Invoice): void**
- Soft deletes invoice
- Restores all stock
- Removes stock movements
- Updates client debt

**getTodaysSummary(): array**
- Returns today's sales metrics
- Total invoices, sales, profit, payments, pending

---

### 2. **ProductService** - Product Management
**File:** `app/Services/ProductService.php`

#### Core Methods:

**createProduct(array $data): Product**
- Creates product with validation
- Checks SKU uniqueness
- Records initial stock as movement
- Uses transaction

**updateProduct(Product, array): Product**
- Updates product details
- Prevents SKU duplication
- Handles stock adjustments
- Uses transaction

**adjustStock(Product, float, string): StockMovement**
- Adjusts inventory manually
- Positive = stock in
- Negative = stock out
- Creates audit trail
- Returns movement record

**getLowStockProducts(): array**
- Returns products below alert threshold
- Shows shortage amount
- Ordered by severity
- Includes category and supplier

**getProductDetails(Product): array**
- Complete product information
- Profit margin percentage
- Total sold and purchased kg
- Recent stock movements

**searchProducts(string, int): Paginator**
- Search by name (EN/AR) or SKU
- Returns paginated results

**getProductsByCategory(int, int): Paginator**
- Filter by main category
- Paginated results

**getProductsBySubCategory(int, int): Paginator**
- Filter by sub category
- Paginated results

**getProductsBySupplier(int, int): Paginator**
- Filter by supplier
- Paginated results

**getAllProductsForSale(int): Paginator**
- Only products with stock > 0
- For POS/invoice system

**getInventorySummary(): array**
- Total products count
- Total stock kg
- Low stock products count
- Total inventory value

**updatePrices(Product, float, float): Product**
- Update purchase and selling prices

**restoreProduct(int): Product**
- Restore soft-deleted product

**permanentlyDeleteProduct(Product): void**
- Force delete (admin only)

---

### 3. **PurchaseService** - Purchase Order Management
**File:** `app/Services/PurchaseService.php`

#### Core Methods:

**createPurchase(array $data): Purchase**
- Creates purchase order
- Generates unique purchase number
- Creates items
- Sets status to 'pending'
- Uses transaction

**Parameters:**
```php
[
    'supplier_id' => int,
    'purchase_date' => '2026-02-19',
    'tax' => float,
    'notes' => string,
    'items' => [
        [
            'product_id' => int,
            'quantity_kg' => float,
            'cost_per_kg' => float,
        ],
        ...
    ]
]
```

**receivePurchase(Purchase, array): Purchase**
- Receives purchase order
- Stock is added to product.current_stock_kg
- Updates product's purchase_price_per_kg to latest cost
- Creates stock movement records
- Updates purchase status

**Stock Addition Flow:**
1. Validate quantity
2. Increment product.current_stock_kg
3. Update product purchase price
4. Create StockMovement with reference_type='purchase'
5. Record audit trail

**calculatePotentialProfit(Purchase): float**
- Profit if all items sold at current prices
- Formula: (selling_price - cost_price) × purchased_quantity

**calculateActualProfit(Purchase): float**
- Profit from items actually sold after purchase
- Uses FIFO logic

**calculateMarkupPercentage(Purchase): float**
- Markup % based on total value vs cost

**getPurchaseDetails(Purchase): array**
- Complete purchase information
- Items with products
- Supplier details
- Potential profit
- Markup percentage

**getPurchasesByDateRange(start, end, int): Paginator**
- Filter by date range
- Paginated results

**getPurchasesBySupplier(int, int): Paginator**
- Filter by supplier
- Paginated results

**getPurchasesByStatus(string, int): Paginator**
- Filter by status (pending/received/partial)
- Paginated results

**getPendingPurchases(): Collection**
- All pending purchases
- With items and supplier

**cancelPurchase(Purchase): void**
- Soft deletes purchase
- Reverts stock if already received
- Removes stock movements

**getPurchaseStatistics(start, end): array**
- Total purchases count
- Total cost
- Total items kg
- Average purchase cost
- Statistics by status

---

### 4. **ReportService** - Financial & Operational Reports
**File:** `app/Services/ReportService.php`

#### Core Methods:

**salesByProduct(start, end): array**
- Total sales per product
- Revenue, cost, profit
- Profit margin %
- Average price
- Sorted by revenue
- Returns totals

**salesByClient(start, end): array**
- Total sales per client
- Total invoices count
- Amount paid vs pending
- Payment rate %
- Sorted by amount

**salesByCategory(start, end): array**
- Sales by main category
- Revenue, cost, profit
- Items count per category
- Profit margin %

**profitReport(start, end): array**
- Gross profit from sales
- Expenses by category
- Total expenses
- Net profit (gross - expenses)
- Summary with invoice counts

**inventoryReport(): array**
- All products current stock
- Stock value calculation
- Low stock products count
- Profit margin per product
- Stock status (low/ok)

**dailyClosingReport(date): array**
- Complete end-of-day summary
- Quick sales vs regular invoices
- Revenue breakdown
- Payments and pending
- Profit summary
- Expenses by category
- Cash flow (in/out/net)

**monthlySalesTrend(months): array**
- Monthly sales trend line
- Revenue and profit per month
- Invoice count per month
- Arabic month names
- Averages

**topPerformingProducts(limit, start, end): array**
- Top selling products
- By revenue
- Limited to N products

**topClients(limit, start, end): array**
- Top clients by sales
- By total amount
- Limited to N clients

---

## Database Transaction Usage

### InvoiceService - Transaction for Invoice Creation
```php
return DB::transaction(function () use ($data) {
    // 1. Create invoice record
    $invoice = Invoice::create([...]);
    
    // 2. For each item:
    //    - Validate stock
    //    - Create invoice item
    //    - Deduct product stock
    //    - Create stock movement
    foreach ($data['items'] as $item) {
        $this->createInvoiceItem($invoice, $item);
    }
    
    // 3. Update client debt
    // 4. Record payment if provided
    
    // All-or-nothing: if any step fails, all rollback
    return $invoice;
});
```

**Why Transaction?**
- Ensures invoice + items + stock movements stay consistent
- If stock update fails, entire invoice creation fails
- Prevents partial invoices in database

### PurchaseService - Transaction for Stock Receipt
```php
return DB::transaction(function () use ($purchase, $receivedItems) {
    foreach ($itemsToReceive as $item) {
        // 1. Increment product stock
        // 2. Update purchase price
        // 3. Create stock movement
    }
    
    // 4. Update purchase status
    return $purchase;
});
```

---

## Stock Movement Audit Trail

Every stock change is logged in `stock_movements` table:

### Invoice (Stock Out)
```php
StockMovement::create([
    'product_id' => $product->id,
    'type' => 'out',  // Type: in or out
    'quantity_kg' => $itemData['quantity_kg'],
    'reference_type' => 'invoice',  // Links to invoice
    'reference_id' => $invoice->id,
    'notes' => "بيع عبر فاتورة #INV-...",
    'created_by' => Auth::id(),
    'created_at' => now(),  // Timestamp
]);
```

### Purchase (Stock In)
```php
StockMovement::create([
    'product_id' => $product->id,
    'type' => 'in',
    'quantity_kg' => $item->quantity_kg,
    'reference_type' => 'purchase',
    'reference_id' => $purchase->id,
    'notes' => "استقبال من طلب شراء #PUR-...",
    'created_by' => Auth::id(),
    'created_at' => now(),
]);
```

### Manual Adjustment
```php
StockMovement::create([
    'product_id' => $product->id,
    'type' => 'in' | 'out',
    'quantity_kg' => $absQuantity,
    'reference_type' => 'adjustment',
    'notes' => "تعديل يدوي",
    'created_by' => Auth::id(),
]);
```

**Benefits:**
- Complete audit trail
- Reconciliation capability
- Trace every stock change to source
- See who changed what, when

---

## Profit Calculation

### Invoice Profit
```php
$profit = 0;
foreach ($invoice->items as $item) {
    $costPrice = $item->product->purchase_price_per_kg;
    $sellingPrice = $item->unit_price;
    $profit += ($sellingPrice - $costPrice) * $item->quantity_kg;
}
```

**Example:**
```
Item: Coffee - Brazilian
- Cost: 50 SAR/kg
- Selling price: 80 SAR/kg
- Quantity: 2 kg
- Profit: (80 - 50) × 2 = 60 SAR
```

### Purchase Potential Profit
```
If you buy at 50 SAR/kg and can sell at 80 SAR/kg
For 10 kg purchase: (80 - 50) × 10 = 300 SAR potential profit
```

---

## Error Handling

All services include try-catch with Arabic messages:

```php
try {
    // Business logic
} catch (Exception $e) {
    throw new Exception('خطأ في إنشاء الفاتورة: ' . $e->getMessage());
}
```

**Arabic Error Messages:**
- "لا يمكن إنشاء فاتورة بدون عناصر" - Cannot create invoice without items
- "المخزون غير كافي للمنتج: ..." - Insufficient stock for product
- "كود المنتج موجود بالفعل" - SKU already exists
- "خطأ في إنشاء الفاتورة: ..." - Error creating invoice

---

## Usage Examples in Controllers

### Create Invoice
```php
$invoiceService = new InvoiceService();

$invoice = $invoiceService->createInvoice([
    'type' => 'regular',
    'client_id' => 1,
    'invoice_date' => '2026-02-19',
    'discount' => 0,
    'amount_paid' => 500,
    'payment_method' => 'cash',
    'items' => [
        [
            'product_id' => 1,
            'quantity_kg' => 2,
            'unit_price' => 250,
        ],
    ],
]);
```

### Quick Sale
```php
$quickSale = $invoiceService->createQuickSale([
    'invoice_date' => now()->toDateString(),
    'items' => [
        [
            'product_id' => 1,
            'quantity_kg' => 1,
        ],
    ],
]);
```

### Create Purchase
```php
$purchaseService = new PurchaseService();

$purchase = $purchaseService->createPurchase([
    'supplier_id' => 1,
    'purchase_date' => '2026-02-19',
    'items' => [
        [
            'product_id' => 1,
            'quantity_kg' => 10,
            'cost_per_kg' => 50,
        ],
    ],
]);

// Receive purchase
$purchaseService->receivePurchase($purchase);
```

### Generate Report
```php
$reportService = new ReportService(new InvoiceService());

$profit = $reportService->profitReport('2026-02-01', '2026-02-28');
$sales = $reportService->salesByProduct('2026-02-01', '2026-02-28');
$inventory = $reportService->inventoryReport();
$dailyClosing = $reportService->dailyClosingReport('2026-02-19');
```

---

## Service Location

```
/app/Services/
├── InvoiceService.php ✅
├── ProductService.php ✅
├── PurchaseService.php ✅
└── ReportService.php ✅
```

---

## Key Features

✅ **Database Transactions** - ACID compliance for critical operations
✅ **Stock Management** - Automatic deduction and addition
✅ **Profit Calculation** - Built into services
✅ **Audit Trail** - Complete stock movement logging
✅ **Error Handling** - Arabic error messages
✅ **Validation** - Stock availability, SKU uniqueness
✅ **Business Logic** - All in services, not controllers
✅ **Pagination** - For report data
✅ **Filtering** - By date, category, supplier, client
✅ **Arabic Support** - All messages and data

---

## Validation Rules Implemented

### InvoiceService
- Items cannot be empty
- Product stock must be sufficient
- Client credit limit can be enforced

### ProductService
- SKU must be unique
- Purchase/selling prices > 0
- Stock quantities valid decimals

### PurchaseService
- Items cannot be empty
- Quantity and cost > 0
- Only pending purchases can be received

### ReportService
- Date range validation
- No negative values in calculations

---

## Next Phase: PHASE 5

**PHASE 5: FORM REQUESTS** will create:
- StoreProductRequest - Validate product creation
- StoreInvoiceRequest - Validate invoice creation
- StoreQuickSaleRequest - Validate quick sales
- StorePurchaseRequest - Validate purchase orders
- StoreClientRequest - Validate client creation

All with Arabic validation messages.

---

## Architecture Benefits

| Benefit | Implementation |
|---------|-----------------|
| **Thin Controllers** | All logic in services |
| **Reusability** | Services usable from any controller |
| **Testability** | Services can be unit tested alone |
| **Maintainability** | Business logic centralized |
| **Scalability** | Easy to add new features |
| **Security** | Validation in service layer |
| **Consistency** | Same logic used everywhere |

---

**Date:** February 19, 2026
**Status:** ✅ PHASE 4 COMPLETE - AWAITING CONFIRMATION FOR PHASE 5
