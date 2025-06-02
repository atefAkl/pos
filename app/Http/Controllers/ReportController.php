<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale; // Added Sale model for fetching sales data

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        // Fetch sales with related data, ordered by newest first, and paginate
        $sales = Sale::with(['customer', 'user', 'items', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(15); // You can adjust the pagination number

        return view('reports.sales', compact('sales'));
    }

    //
}
