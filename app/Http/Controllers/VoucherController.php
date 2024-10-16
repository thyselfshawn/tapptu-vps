<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use App\DataTables\VouchersDataTable;

class VoucherController extends Controller
{
    public function __construct()
    {
        // Only admins can access
        $this->middleware('admin')->only(['create', 'store', 'show', 'edit', 'update', 'destroy']);
    }
    public function index(VouchersDataTable $dataTable)
    {
        return $dataTable->render('vouchers.index');
    }

    public function create()
    {
        return view('vouchers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'uuid' => 'required|string|max:255',
            'venue_id' => 'required|exists:venues,id',
            'review_id' => 'nullable|exists:reviews,id',
            'voucher' => 'required|numeric',
            'status' => 'required|string',
        ]);

        Voucher::create($validated);

        return redirect()->route('vouchers.index')->with('success', 'Voucher created successfully.');
    }

    public function show(Voucher $voucher)
    {
        return view('vouchers.show', compact('voucher'));
    }

    public function edit(Voucher $voucher)
    {
        return view('vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'uuid' => 'required|string|max:255',
            'venue_id' => 'required|exists:venues,id',
            'review_id' => 'nullable|exists:reviews,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'voucher' => 'required|numeric',
            'status' => 'required|string',
        ]);

        $voucher->update($validated);

        return redirect()->route('vouchers.index')->with('success', 'Voucher updated successfully.');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();

        return redirect()->route('vouchers.index')->with('success', 'Voucher deleted successfully.');
    }
}

