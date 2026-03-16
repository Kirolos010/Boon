<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Http\Requests\StoreSupplierRequest;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        try {
            $suppliers = Supplier::query()
                ->when($request->filled('search'), function ($query) use ($request) {
                    $search = trim((string) $request->input('search'));

                    $query->where(function ($q) use ($search) {
                        $q->where('name_ar', 'like', "%{$search}%")
                            ->orWhere('name_en', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withQueryString();

            return view('settings.suppliers.index', compact('suppliers'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function create()
    {
        return view('settings.suppliers.create');
    }

    public function store(StoreSupplierRequest $request)
    {
        try {
            $supplier = Supplier::create($request->validated());
            return redirect()->route('settings.suppliers.index')
                ->with('success', 'تم إضافة المورد بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit(Supplier $supplier)
    {
        return view('settings.suppliers.edit', compact('supplier'));
    }

    public function update(StoreSupplierRequest $request, Supplier $supplier)
    {
        try {
            $supplier->update($request->validated());
            return redirect()->route('settings.suppliers.index')
                ->with('success', 'تم تحديث المورد بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();
            return redirect()->route('settings.suppliers.index')
                ->with('success', 'تم حذف المورد بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
