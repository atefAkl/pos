<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    /**
     * Display a listing of the taxes.
     */
    public function index()
    {
        $taxes = Tax::orderBy('name')->get();
        return view('settings.taxes.index', compact('taxes'));
    }

    /**
     * Show the form for creating a new tax.
     */
    public function create()
    {
        return view('settings.taxes.create');
    }

    /**
     * Store a newly created tax in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset all other defaults
        if ($request->boolean('is_default')) {
            Tax::where('is_default', true)->update(['is_default' => false]);
        }

        Tax::create($validated);

        return redirect()->route('settings.taxes.index')
            ->with('success', 'تم إنشاء الضريبة بنجاح');
    }

    /**
     * Show the form for editing the specified tax.
     */
    public function edit(Tax $tax)
    {
        return view('settings.taxes.edit', compact('tax'));
    }

    /**
     * Update the specified tax in storage.
     */
    public function update(Request $request, Tax $tax)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset all other defaults
        if ($request->boolean('is_default') && !$tax->is_default) {
            Tax::where('is_default', true)->update(['is_default' => false]);
        }

        $tax->update($validated);

        return redirect()->route('settings.taxes.index')
            ->with('success', 'تم تحديث الضريبة بنجاح');
    }

    /**
     * Toggle the active status of the specified tax.
     */
    public function toggleStatus(Tax $tax)
    {
        // Don't allow deactivating the default tax
        if ($tax->is_default && $tax->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تعطيل الضريبة الافتراضية'
            ], 422);
        }

        $tax->update([
            'is_active' => !$tax->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الضريبة بنجاح',
            'is_active' => $tax->is_active
        ]);
    }

    /**
     * Remove the specified tax from storage.
     */
    public function destroy(Tax $tax)
    {
        // Check if tax is used by any products
        if ($tax->products()->count() > 0) {
            return redirect()->route('settings.taxes.index')
                ->with('error', 'لا يمكن حذف الضريبة لأنها مستخدمة في منتجات');
        }

        // Don't allow deleting the default tax
        if ($tax->is_default) {
            return redirect()->route('settings.taxes.index')
                ->with('error', 'لا يمكن حذف الضريبة الافتراضية');
        }

        $tax->delete();

        return redirect()->route('settings.taxes.index')
            ->with('success', 'تم حذف الضريبة بنجاح');
    }
}
