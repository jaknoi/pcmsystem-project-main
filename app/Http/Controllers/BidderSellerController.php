<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Seller;
use App\Models\Bidder;
use App\Models\Info;

class BidderSellerController extends Controller
{
    // แสดงฟอร์มสำหรับการเพิ่มผู้ขายและเจ้าหน้าที่
    public function index($id = null)
    {
        // ดึงข้อมูล Info ตาม id หรือสร้าง Info ใหม่
        $info = $id ? Info::with(['sellers', 'bidders'])->findOrFail($id) : new Info();
        
        if ($id) {
            $info = Info::with(['sellers', 'bidders'])->findOrFail($id);
        } else {
            $info = new Info();  // กรณี id เป็น null ให้สร้าง Info ใหม่
        }
        // ดึงข้อมูลผู้ขายและเจ้าหน้าที่ทั้งหมด
        $sellers = Seller::all();
        $bidders = Bidder::all();
    
        // ส่งข้อมูลไปยัง view
        return view('bidders_sellers.index', compact('sellers', 'bidders', 'info'));
    }
    

    public function storeSeller(Request $request, $id = null)
    {
        $info = $id ? Info::with(['sellers', 'bidders'])->findOrFail($id) : new Info();
    
        // ตรวจสอบความถูกต้องของข้อมูลผู้ขาย
        $request->validate([
            'seller_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'taxpayer_number' => 'required|string|max:255',
        ]);
    
        // ตรวจสอบว่ามี info_id หรือไม่
        $data = $request->only('seller_name', 'address', 'taxpayer_number');
        if ($id) {
            $data['info_id'] = $id; // หากมี id ให้บันทึก
        }
    
        // บันทึกข้อมูลผู้ขาย
        Seller::create($data);
    
        // ส่งกลับพร้อมข้อความสำเร็จ
        return redirect()->route('bidders_sellers.index', ['id' => $id])->with('success', 'ข้อมูลผู้ขายบันทึกสำเร็จ!');
    }
    
    public function storeBidder(Request $request, $id = null)
    {
        $info = $id ? Info::with(['sellers', 'bidders'])->findOrFail($id) : new Info();
    
        // ตรวจสอบความถูกต้องของข้อมูลเจ้าหน้าที่
        $request->validate([
            'bidder_name' => 'required|string|max:255',
            'bidder_position' => 'required|string|max:255',
        ]);
    
        // ตรวจสอบว่ามี info_id หรือไม่
        $data = $request->only('bidder_name', 'bidder_position');
        if ($id) {
            $data['info_id'] = $id; // หากมี id ให้บันทึก
        }
    
        // บันทึกข้อมูลเจ้าหน้าที่
        Bidder::create($data);
    
        // ส่งกลับพร้อมข้อความสำเร็จ
        return redirect()->route('bidders_sellers.index', ['id' => $id])->with('success', 'ข้อมูลเจ้าหน้าที่บันทึกสำเร็จ!');
    }
    


    
    public function editSeller($id)
    {
        // หาผู้ขายโดย id เท่านั้น ไม่ต้องอ้างถึง info_id
        $seller = Seller::findOrFail($id);
        return view('bidders_sellers.editSeller', compact('seller'));
    }
    
    public function updateSeller(Request $request, $id)
    {
        // หาผู้ขายโดย id เท่านั้น
        $seller = Seller::findOrFail($id);
        $seller->update($request->all());
        return redirect()->route('bidders_sellers.index')->with('success', 'แก้ไขข้อมูลผู้ขายสำเร็จ!');
    }
    
    public function deleteSeller($id)
{
    // หาผู้ขายโดย id เท่านั้น
    $seller = Seller::findOrFail($id);

    // ลบผู้ขายทั้งหมดที่มีชื่อเดียวกับผู้ขายที่กำลังถูกลบ
    Seller::where('seller_name', $seller->seller_name)->delete();

    return redirect()->route('bidders_sellers.index')->with('success', 'ลบข้อมูลผู้ขายสำเร็จ!');
}

    

    public function editBidder($id)
    {
        // หาผู้ขายโดย id เท่านั้น ไม่ต้องอ้างถึง info_id
        $bidder = Bidder::findOrFail($id);
        return view('bidders_sellers.editBidder', compact('bidder'));
    }
    
    public function updateBidder(Request $request, $id)
    {
        // หาผู้ขายโดย id เท่านั้น
        $bidder = Bidder::findOrFail($id);
        $bidder->update($request->all());
        return redirect()->route('bidders_sellers.index')->with('success', 'แก้ไขข้อมูลเจ้าหน้าที่สำเร็จ!');
    }
    
    public function deleteBidder($id)
{
    // หาเจ้าหน้าที่โดย id เท่านั้น
    $bidder = Bidder::findOrFail($id);

    // ลบเจ้าหน้าที่ทั้งหมดที่มีชื่อเดียวกับเจ้าหน้าที่ที่กำลังถูกลบ
    Bidder::where('bidder_name', $bidder->bidder_name)->delete();

    return redirect()->route('bidders_sellers.index')->with('success', 'ลบข้อมูลเจ้าหน้าที่สำเร็จ!');
}

    


}
