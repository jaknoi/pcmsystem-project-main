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
    
        // Retrieve monthly data grouped by month
        $monthlyData = Info::with(['sellers', 'products'])
            ->whereYear('date', $currentYear)
            ->get();
    
        // Group monthly data
        $groupedMonthlyData = $monthlyData->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->date)->format('Y-m');
        });
    
        // Retrieve quarterly data grouped by quarter
        $quarterlyData = Info::with(['sellers', 'products'])
            ->whereYear('date', $currentYear)
            ->get();
        
        // Group quarterly data
        $groupedQuarterlyData = $quarterlyData->groupBy(function ($item) {
            $month = \Carbon\Carbon::parse($item->date)->month;
            $quarter = ceil($month / 3); // Determine the quarter
            return 'Q' . $quarter . ' ' . \Carbon\Carbon::parse($item->date)->year;
        });
    
        // Pass the grouped data to the view
        return view('summary', compact('groupedMonthlyData', 'groupedQuarterlyData', 'currentYear', 'quarterlyData'));
    }
    
}
