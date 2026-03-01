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
        $products = Product::with(['mainCategory', 'subCategory', 'supplier'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('products.index', ['products' => $products]);
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
            $reference = $request->input('reference') ?? 'adjustment';
            $notes = $request->input('notes') ?? '';

            // Build notes message
            $notesMessage = "[$reference] $notes";

            $movement = $this->productService->adjustStock(
                $product,
                $request->input('quantity'),
                $notesMessage
            );

            return redirect()->route('products.show', $product)
                ->with('success', 'تم تعديل المخزون بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
