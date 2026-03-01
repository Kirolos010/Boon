<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Product;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    public function __construct(protected InvoiceService $invoiceService)
    {
    }

    public function index(Request $request)
    {
        $invoices = Invoice::with('client', 'items')
            ->whereNotNull('client_id')
            ->where('type', 'regular')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('invoices.index', ['invoices' => $invoices]);
    }

    public function create()
    {
        $clients = Client::active()->get();
        $products = Product::with(['mainCategory', 'subCategory'])
            ->where('current_stock_kg', '>', 0)
            ->get();

        return view('invoices.create', compact('clients', 'products'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        try {
            $data = $request->validated();
            $invoice = $this->invoiceService->createInvoice($data);

            return redirect()->route('invoices.index')
                ->with('success', 'تم إنشاء الفاتورة بنجاح');
        } catch (\Exception $e) {
            Log::error('Invoice creation error: ' . $e->getMessage() . ' | ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'خطأ: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(string $id)
    {
        $invoice = Invoice::with('client', 'items.product', 'payments')
            ->findOrFail($id);

        return view('invoices.show', ['invoice' => $invoice]);
    }

    public function edit(string $id)
    {
        $invoice = Invoice::with('items')
            ->findOrFail($id);
        $clients = Client::all();
        $products = Product::with(['mainCategory', 'subCategory'])->get();

        return view('invoices.edit', compact('invoice', 'clients', 'products'));
    }

    public function update(StoreInvoiceRequest $request, string $id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $updated = $this->invoiceService->updateInvoice($invoice, $request->validated());

            return redirect()->route('invoices.index')
                ->with('success', 'تم تحديث الفاتورة بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $this->invoiceService->cancelInvoice($invoice);

            return redirect()->route('invoices.index')
                ->with('success', 'تم إلغاء الفاتورة بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function recordPayment(Request $request, string $id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $payment = $this->invoiceService->recordPayment(
                $invoice,
                $request->input('amount'),
                $request->input('payment_method'),
                $request->input('payment_date'),
                $request->input('notes')
            );

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'تم تسجيل الدفعة بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
