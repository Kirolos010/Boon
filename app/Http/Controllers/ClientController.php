<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        try {
            $clients = Client::with('invoices')
                ->when($request->filled('search'), function ($query) use ($request) {
                    $search = trim((string) $request->input('search'));

                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('name_ar', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                })
                ->when($request->filled('status'), function ($query) use ($request) {
                    $status = $request->input('status');

                    if ($status === 'active') {
                        $query->where('is_active', true);
                    }

                    if ($status === 'inactive') {
                        $query->where('is_active', false);
                    }
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withQueryString();

            return view('clients.index', ['clients' => $clients]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(StoreClientRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = $request->user()->id;
            $client = Client::create($data);

            return redirect()->route('clients.index')
                ->with('success', 'تم إنشاء العميل بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(string $id)
    {
        $client = Client::with('invoices.items', 'invoices.payments')
            ->findOrFail($id);

        return view('clients.show', ['client' => $client]);
    }

    public function edit(string $id)
    {
        $client = Client::findOrFail($id);

        return view('clients.edit', ['client' => $client]);
    }

    public function update(StoreClientRequest $request, string $id)
    {
        try {
            $client = Client::findOrFail($id);
            $client->update($request->validated());

            return redirect()->route('clients.index')
                ->with('success', 'تم تحديث بيانات العميل بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $client = Client::findOrFail($id);
            $client->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف العميل بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function invoices(string $id)
    {
        $client = Client::findOrFail($id);
        $invoices = $client->invoices()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('clients.invoices', compact('client', 'invoices'));
    }
}
