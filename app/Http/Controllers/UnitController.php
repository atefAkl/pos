<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the units.
     */
    public function index()
    {
        $units = Unit::orderBy('name')->get();
        return view('settings.units.index', compact('units'));
    }

    /**
     * Show the form for creating a new unit.
     */
    public function create()
    {
        return view('settings.units.create');
    }

    /**
     * Store a newly created unit in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:units',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset all other defaults
        if ($request->boolean('is_default')) {
            Unit::where('is_default', true)->update(['is_default' => false]);
        }

        Unit::create($validated);

        return redirect()->route('settings.units.index')
            ->with('success', 'تم إنشاء وحدة القياس بنجاح');
    }

    /**
     * Show the form for editing the specified unit.
     */
    public function edit(Unit $unit)
    {
        return view('settings.units.edit', compact('unit'));
    }

    /**
     * Update the specified unit in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:units,code,' . $unit->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset all other defaults
        if ($request->boolean('is_default') && !$unit->is_default) {
            Unit::where('is_default', true)->update(['is_default' => false]);
        }

        $unit->update($validated);

        return redirect()->route('settings.units.index')
            ->with('success', 'تم تحديث وحدة القياس بنجاح');
    }

    /**
     * Toggle the active status of the specified unit.
     */
    public function toggleStatus(Unit $unit)
    {
        // Don't allow deactivating the default unit
        if ($unit->is_default && $unit->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تعطيل وحدة القياس الافتراضية'
            ], 422);
        }

        $unit->update([
            'is_active' => !$unit->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة وحدة القياس بنجاح',
            'is_active' => $unit->is_active
        ]);
    }

    /**
     * Remove the specified unit from storage.
     */
    public function destroy(Unit $unit)
    {
        // Check if unit is used by any products
        if ($unit->products()->count() > 0) {
            return redirect()->route('settings.units.index')
                ->with('error', 'لا يمكن حذف وحدة القياس لأنها مستخدمة في منتجات');
        }

        // Don't allow deleting the default unit
        if ($unit->is_default) {
            return redirect()->route('settings.units.index')
                ->with('error', 'لا يمكن حذف وحدة القياس الافتراضية');
        }

        $unit->delete();

        return redirect()->route('settings.units.index')
            ->with('success', 'تم حذف وحدة القياس بنجاح');
    }
}
