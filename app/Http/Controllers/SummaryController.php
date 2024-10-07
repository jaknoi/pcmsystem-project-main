<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Info;

class SummaryController extends Controller
{
    public function showSummary()
    {
        $currentYear = date('Y');

        // Retrieve monthly data
        $monthlyData = Info::with('sellers')->whereYear('date', $currentYear)->get();
        
        // Group monthly data
        $groupedMonthlyData = $monthlyData->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->date)->format('Y-m'); // Group by year-month
        });

        // Retrieve quarterly data
        $quarterlyData = Info::with('sellers')->whereYear('date', $currentYear)->get();
        
        // Group quarterly data
        $groupedQuarterlyData = $quarterlyData->groupBy(function ($item) {
            $month = \Carbon\Carbon::parse($item->date)->month;
            $quarter = ceil($month / 3); // Determine the quarter
            return 'Q' . $quarter . ' ' . \Carbon\Carbon::parse($item->date)->year; // Group by quarter
        });
    
        return view('summary', compact('groupedMonthlyData', 'groupedQuarterlyData', 'currentYear'));
    }
    
    public function filterSummary(Request $request)
{
    $currentYear = $request->input('year', date('Y')); // Get the selected year or default to current year
    
    // Adjust for fiscal year, treat October to December as the first quarter of the fiscal year
    $fiscalYearStart = $currentYear - 1; // Fiscal year starts in October of the previous year

    // Retrieve monthly data grouped by fiscal year (October to September)
    $monthlyData = Info::with(['sellers', 'products'])
        ->where(function ($query) use ($currentYear, $fiscalYearStart) {
            $query->whereYear('date', $fiscalYearStart)
                  ->whereMonth('date', '>=', 10) // From October to December of the previous year
                  ->orWhereYear('date', $currentYear)
                  ->whereMonth('date', '<=', 9); // From January to September of the current year
        })
        ->get();

    // Group monthly data based on fiscal year
    $groupedMonthlyData = $monthlyData->groupBy(function ($item) {
        return \Carbon\Carbon::parse($item->date)->format('Y-m');
    });

    // Retrieve quarterly data grouped by fiscal quarters
    $quarterlyData = Info::with(['sellers', 'products'])
        ->where(function ($query) use ($currentYear, $fiscalYearStart) {
            $query->whereYear('date', $fiscalYearStart)
                  ->whereMonth('date', '>=', 10) // From October to December of the previous year
                  ->orWhereYear('date', $currentYear)
                  ->whereMonth('date', '<=', 9); // From January to September of the current year
        })
        ->get();

    // Group quarterly data based on fiscal year quarters
    $groupedQuarterlyData = $quarterlyData->groupBy(function ($item) {
        $month = \Carbon\Carbon::parse($item->date)->month;
        // Adjust for fiscal quarters starting in October
        $quarter = $month >= 10 ? ceil(($month - 9) / 3) : ceil($month / 3) + 3; 
        return 'Q' . $quarter . ' ' . \Carbon\Carbon::parse($item->date)->year;
    });

    // Pass the grouped data to the view
    return view('summary', compact('groupedMonthlyData', 'groupedQuarterlyData', 'currentYear', 'quarterlyData'));
}

    
}
