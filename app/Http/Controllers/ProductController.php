<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Models\MainCategory;
use App\Models\SubCategory;
use App\Models\Supplier;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService)
    {
    }

    public function index(Request $request)
    {
        try {
            $mainCategories = MainCategory::orderBy('name_ar')->get(['id', 'name_ar', 'name']);

            $products = Product::with(['mainCategory', 'subCategory', 'supplier'])
                ->when($request->filled('search'), function ($query) use ($request) {
                    $search = trim((string) $request->input('search'));

                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('name_ar', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
                })
                ->when($request->filled('main_category_id'), function ($query) use ($request) {
                    $query->where('main_category_id', (int) $request->input('main_category_id'));
                })
                ->when($request->filled('status'), function ($query) use ($request) {
                    $status = (string) $request->input('status');

                    if ($status === 'in-stock') {
                        $query->whereColumn('current_stock_kg', '>', 'minimum_stock_alert');
                    }

                    if ($status === 'low-stock') {
                        $query->where('current_stock_kg', '>', 0)
                            ->whereColumn('current_stock_kg', '<=', 'minimum_stock_alert');
                    }

                    if ($status === 'out') {
                        $query->where('current_stock_kg', '<=', 0);
                    }
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withQueryString();

            return view('products.index', [
                'products' => $products,
                'mainCategories' => $mainCategories,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function create()
    {
        $mainCategories = MainCategory::all();
        $subCategories = SubCategory::all();
        $suppliers = Supplier::all();

        return view('products.create', compact('mainCategories', 'subCategories', 'suppliers'));
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $product = $this->productService->createProduct($request->validated());

            return redirect()->route('products.index')
                ->with('success', 'تم إنشاء المنتج بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(string $id)
    {
        $product = Product::with(['mainCategory', 'subCategory', 'supplier', 'creator', 'stockMovements'])->findOrFail($id);
        $details = $this->productService->getProductDetails($product);

        return view('products.show', ['product' => $product, 'details' => $details]);
    }

    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $mainCategories = MainCategory::all();
        $subCategories = SubCategory::all();
        $suppliers = Supplier::all();

        return view('products.edit', compact('product', 'mainCategories', 'subCategories', 'suppliers'));
    }

    public function update(StoreProductRequest $request, string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $updated = $this->productService->updateProduct($product, $request->validated());

            return redirect()->route('products.index')
                ->with('success', 'تم تحديث المنتج بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $this->productService->permanentlyDeleteProduct($product);

            return redirect()->route('products.index')
                ->with('success', 'تم حذف المنتج بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function lowStock()
    {
        $lowStockProducts = $this->productService->getLowStockProducts();

        return response()->json([
            'status' => 'success',
            'data' => $lowStockProducts,
        ]);
    }

    public function getSubcategories(string $mainCategoryId)
    {
        try {
            $mainCategory = MainCategory::findOrFail($mainCategoryId);
            $subcategories = $mainCategory->subCategories;

            return response()->json([
                'status' => 'success',
                'data' => $subcategories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'فئة غير صحيحة',
            ], 404);
        }
    }

    public function generateSku(string $productName)
    {
        try {
            $sku = $this->productService->generateSkuForDisplay($productName);

            return response()->json([
                'status' => 'success',
                'sku' => $sku,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'خطأ في توليد الكود',
            ], 500);
        }
    }

    public function adjustStock(Request $request, string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $validated = $request->validate([
                'quantity' => 'required|numeric|not_in:0',
                'reference' => 'required|string|in:adjustment,purchase,sales,inventory,return',
                'notes' => 'nullable|string|max:1000',
            ]);

            $reference = $request->input('reference') ?? 'adjustment';
            $notes = $request->input('notes') ?? '';

            $movement = $this->productService->adjustStock(
                $product,
                (float) $validated['quantity'],
                (string) $validated['reference'],
                (string) ($validated['notes'] ?? '')
            );

            return redirect()->route('products.show', $product)
                ->with('success', 'تم تعديل المخزون بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
