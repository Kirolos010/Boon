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
            ->paginate(15);

        return view('quick-sales.index', ['quickSales' => $quickSales]);
    }

    public function create()
    {
        $products = Product::all();

        return view('quick-sales.create', compact('products'));
    }

    public function store(StoreQuickSaleRequest $request)
    {
        try {
            $data = $request->validated();
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
            ->with('items.product', 'payments')
            ->firstOrFail();
        $details = $this->invoiceService->getInvoiceDetails($sale);

        return response()->json([
            'status' => 'success',
            'data' => $details,
        ]);
    }

    public function destroy(string $id)
    {
        try {
            $sale = Invoice::findOrFail($id);
            $this->invoiceService->cancelInvoice($sale);

            return response()->json([
                'status' => 'success',
                'message' => 'تم إلغاء عملية البيع بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
