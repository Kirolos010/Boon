<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuickSaleRequest;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class QuickSaleController extends Controller
{
    public function __construct(protected InvoiceService $invoiceService)
    {
    }

    public function index(Request $request)
    {
        $quickSales = Invoice::where('type', 'quick')
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('quick-sales.index', ['quickSales' => $quickSales]);
    }

    public function create()
    {
        $products = Product::with(['mainCategory', 'subCategory'])->get();

        return view('quick-sales.create', compact('products'));
    }

    public function store(StoreQuickSaleRequest $request)
    {
        try {
            $data = $request->validated();

            // Set default values if not provided
            $data['invoice_date'] = $data['sale_date'] ?? now()->toDateString();
            unset($data['sale_date']);

            // Set default payment method if not provided
            if (empty($data['payment_method'])) {
                $data['payment_method'] = 'cash';
            }

            // Transform items data
            foreach ($data['items'] as &$item) {
                $item['quantity_kg'] = $item['quantity'];
                $item['unit_price'] = $item['price'];
                unset($item['quantity']);
                unset($item['price']);
            }

            $sale = $this->invoiceService->createQuickSale($data);

            return redirect()->route('quick-sales.index')
                ->with('success', 'تم تسجيل البيع السريع بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(string $id)
    {
        $sale = Invoice::where('type', 'quick')
            ->where('id', $id)
            ->with('items.product')
            ->firstOrFail();

        return view('quick-sales.show', compact('sale'));
    }

    public function destroy(string $id)
    {
        try {
            $sale = Invoice::findOrFail($id);
            $this->invoiceService->cancelInvoice($sale);

            return redirect()->route('quick-sales.index')
                ->with('success', 'تم إلغاء عملية البيع بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
