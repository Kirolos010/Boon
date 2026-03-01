<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::with('invoices')
            ->paginate(15);

        return view('clients.index', ['clients' => $clients]);
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
        $invoices = $client->invoices()->paginate(15);

        return view('clients.invoices', compact('client', 'invoices'));
    }
}
