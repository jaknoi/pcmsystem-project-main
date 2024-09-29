<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Info;

class SummaryController extends Controller
{
    public function showSummary()
    {
        // ดึงข้อมูลรายเดือน
        $monthlyData = Info::with('sellers', 'products') // รวมข้อมูลผู้ขายและผลิตภัณฑ์
            ->whereMonth('created_at', date('m')) // ดึงข้อมูลของเดือนนี้
            ->get()
            ->map(function($info) {
                // คำนวณราคาสินค้ารวม
                $totalPrice = $info->products->sum(function($product) {
                    return $product->quantity * $product->product_price;
                });
                $info->total_price = $totalPrice; // เพิ่มยอดรวมใน $info
                return $info;
            });
    
        // ดึงข้อมูลรายไตรมาส
        $currentMonth = date('n'); // เดือนปัจจุบัน
        $currentQuarter = ceil($currentMonth / 3); // คำนวณไตรมาสจากเดือน
    
        $quarterlyData = Info::with('sellers', 'products') // รวมข้อมูลผู้ขายและผลิตภัณฑ์
            ->whereRaw('QUARTER(created_at) = ?', [$currentQuarter]) // ดึงข้อมูลของไตรมาสนี้
            ->get()
            ->map(function($info) {
                // คำนวณราคาสินค้ารวม
                $totalPrice = $info->products->sum(function($product) {
                    return $product->quantity * $product->product_price;
                });
                $info->total_price = $totalPrice; // เพิ่มยอดรวมใน $info
                return $info;
            });
    
        return view('summary', compact('monthlyData', 'quarterlyData'));
    }
    
}
