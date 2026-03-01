<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $expenses = Expense::with('category', 'creator')
            ->paginate(15);

        return view('expenses.index', ['expenses' => $expenses]);
    }

    public function create()
    {
        $categories = ExpenseCategory::all();

        return view('expenses.create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'expense_category_id' => 'required|exists:expense_categories,id',
                'description' => 'required|string|max:1000',
                'amount' => 'required|numeric|min:0.01',
                'expense_date' => 'required|date',
                'reference' => 'nullable|string|max:100',
                'notes' => 'nullable|string|max:1000',
            ]);

            $validated['created_by'] = Auth::id();
            $expense = Expense::create($validated);

            return redirect()->route('expenses.index')
                ->with('success', 'تم تسجيل النفقة بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(string $id)
    {
        $expense = Expense::with('category', 'creator')
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $expense,
        ]);
    }

    public function edit(string $id)
    {
        $expense = Expense::findOrFail($id);
        $categories = ExpenseCategory::all();

        return view('expenses.edit', [
            'expense' => $expense,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, string $id)
    {
        try {
            $expense = Expense::findOrFail($id);
            $validated = $request->validate([
                'expense_category_id' => 'required|exists:expense_categories,id',
                'description' => 'required|string|max:1000',
                'amount' => 'required|numeric|min:0.01',
                'expense_date' => 'required|date',
                'reference' => 'nullable|string|max:100',
                'notes' => 'nullable|string|max:1000',
            ]);

            $expense->update($validated);

            return redirect()->route('expenses.index')
                ->with('success', 'تم تحديث بيانات النفقة بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $expense = Expense::findOrFail($id);
            $expense->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف بيانات النفقة بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
