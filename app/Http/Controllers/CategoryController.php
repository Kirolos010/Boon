<?php

namespace App\Http\Controllers;

use App\Models\MainCategory;
use App\Models\SubCategory;
use App\Http\Requests\StoreCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $mainCategories = MainCategory::with('subCategories')
                ->when($request->filled('search'), function ($query) use ($request) {
                    $search = trim((string) $request->input('search'));

                    $query->where(function ($q) use ($search) {
                        $q->where('name_ar', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
                })
                ->when($request->filled('type'), function ($query) use ($request) {
                    $type = (string) $request->input('type');

                    if ($type === 'with_subcategories') {
                        $query->has('subCategories');
                    }

                    if ($type === 'without_subcategories') {
                        $query->doesntHave('subCategories');
                    }
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withQueryString();

            return view('settings.categories.index', compact('mainCategories'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function create()
    {
        return view('settings.categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $data = $request->validated();
            $subcategories = $data['subcategories'] ?? [];

            // Create main category
            $mainCategory = MainCategory::create([
                'name_ar' => $data['name_ar'],
            ]);

            // Create subcategories - filter out empty ones
            if (!empty($subcategories)) {
                foreach ($subcategories as $sub) {
                    if (!empty($sub['name_ar']) && trim($sub['name_ar']) !== '') {
                        SubCategory::create([
                            'main_category_id' => $mainCategory->id,
                            'name_ar' => trim($sub['name_ar']),
                        ]);
                    }
                }
            }

            return redirect()->route('settings.categories.index')
                ->with('success', 'تم إضافة الفئة والفئات الفرعية بنجاح');
        } catch (\Exception $e) {
            Log::error('Category creation error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(MainCategory $category)
    {
        $category->load('subCategories');
        return view('settings.categories.edit', ['mainCategory' => $category]);
    }

    public function update(StoreCategoryRequest $request, MainCategory $category)
    {
        try {
            $data = $request->validated();
            $subcategories = $data['subcategories'] ?? [];

            // Update main category
            $category->update([
                'name_ar' => $data['name_ar'],
            ]);

            // Delete existing subcategories
            $category->subCategories()->delete();

            // Create new subcategories - filter out empty ones
            if (!empty($subcategories)) {
                foreach ($subcategories as $sub) {
                    if (!empty($sub['name_ar']) && trim($sub['name_ar']) !== '') {
                        SubCategory::create([
                            'main_category_id' => $category->id,
                            'name_ar' => trim($sub['name_ar']),
                        ]);
                    }
                }
            }

            return redirect()->route('settings.categories.index')
                ->with('success', 'تم تحديث الفئة والفئات الفرعية بنجاح');
        } catch (\Exception $e) {
            Log::error('Category update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(MainCategory $category)
    {
        try {
            $category->delete();
            return redirect()->route('settings.categories.index')
                ->with('success', 'تم حذف الفئة بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ============= SUB-CATEGORIES METHODS =============

    public function showSubcategories(MainCategory $category, Request $request)
    {
        $search = $request->get('search', '');

        $subcategories = $category->subCategories();

        if ($search) {
            $subcategories->where('name_ar', 'like', '%' . $search . '%');
        }

        $subcategories = $subcategories->paginate(15);

        return view('settings.categories.subcategories', compact('category', 'subcategories', 'search'));
    }

    public function storeSubcategory(Request $request, MainCategory $category)
    {
        try {
            $validated = $request->validate([
                'name_ar' => 'required|string|max:255',
            ], [
                'name_ar.required' => 'اسم الفئة الفرعية مطلوب',
            ]);

            SubCategory::create([
                'main_category_id' => $category->id,
                'name_ar' => $validated['name_ar'],
            ]);

            return redirect()->back()->with('success', 'تم إضافة الفئة الفرعية بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function updateSubcategory(Request $request, MainCategory $category, SubCategory $subcategory)
    {
        try {
            $validated = $request->validate([
                'name_ar' => 'required|string|max:255',
            ], [
                'name_ar.required' => 'اسم الفئة الفرعية مطلوب',
            ]);

            if ($subcategory->main_category_id !== $category->id) {
                return back()->withErrors(['error' => 'الفئة الفرعية غير مرتبطة بهذه الفئة']);
            }

            $subcategory->update(['name_ar' => $validated['name_ar']]);

            return redirect()->back()->with('success', 'تم تحديث الفئة الفرعية بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroySubcategory(MainCategory $category, SubCategory $subcategory)
    {
        try {
            if ($subcategory->main_category_id !== $category->id) {
                return back()->withErrors(['error' => 'الفئة الفرعية غير مرتبطة بهذه الفئة']);
            }

            $subcategory->delete();

            return redirect()->back()->with('success', 'تم حذف الفئة الفرعية بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
