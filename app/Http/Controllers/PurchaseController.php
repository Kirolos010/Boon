<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;
use App\Services\PurchaseService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(protected PurchaseService $purchaseService)
    {
    }

    public function index(Request $request)
    {
        $purchases = Purchase::with('supplier', 'items')
            ->paginate(15);

        return view('purchases.index', ['purchases' => $purchases]);
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(StorePurchaseRequest $request)
    {
        try {
            $purchase = $this->purchaseService->createPurchase($request->validated());

            return redirect()->route('purchases.index')
                ->with('success', 'تم إنشاء طلب الشراء بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(string $id)
    {
        $purchase = Purchase::with('supplier', 'items.product')
            ->findOrFail($id);

        return view('purchases.show', ['purchase' => $purchase]);
    }

    public function edit(string $id)
    {
        $purchase = Purchase::with('items')
            ->findOrFail($id);
        $suppliers = Supplier::all();
        $products = Product::all();

        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function update(StorePurchaseRequest $request, string $id)
    {
        try {
            $purchase = Purchase::findOrFail($id);
            $purchase->update($request->validated());

            return redirect()->route('purchases.index')
                ->with('success', 'تم تحديث طلب الشراء بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $purchase = Purchase::findOrFail($id);
            $this->purchaseService->cancelPurchase($purchase);

            return redirect()->route('purchases.index')
                ->with('success', 'تم إلغاء طلب الشراء بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function receive(Request $request, string $id)
    {
        try {
            $purchase = Purchase::findOrFail($id);
            $updated = $this->purchaseService->receivePurchase(
                $purchase,
                $request->input('items', [])
            );

            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'تم استلام الطلبية بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
