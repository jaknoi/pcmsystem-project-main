<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Info;
use App\Models\Product;
use App\Models\Seller;
use App\Models\CommitteeMember;
use App\Models\Bidder;
use App\Models\Inspector;
use Carbon\Carbon;
use App\Models\History;
use App\Models\Budget;
use Barryvdh\DomPDF\Facade\Pdf;
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            // บันทึกกิจกรรมการล็อกอิน
            History::create([
                'user_id' => Auth::id(),
                'activity' => 'Login',
                'details' => 'ผู้ใช้เข้าสู่ระบบ',
            ]);
    
            return redirect()->intended('page')
                        ->with('message', 'Signed in!');
        }
    
        return redirect('/login')->with('message', 'Login details are not valid!');

    }
    

    public function signup()
    {
        return view('registration');
    }

    public function signupsave(Request $request)
    {  
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $data = $request->all();
        $this->create($data);

        return redirect("page");
    }

    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }    

    public function index()
    {
        if (Auth::check()) {
            return view('page.index');
        }
        return redirect('/login');
    }

    public function signOut() 
{
    // บันทึกกิจกรรมการออกจากระบบ
    History::create([
        'user_id' => Auth::id(),
        'activity' => 'Logout',
        'details' => 'ผู้ใช้ออกจากระบบ',
    ]);

    Session::flush();
    Auth::logout();

    return redirect('login');
}

public function list(Request $request)
{
    $search = $request->query('search');

    // สร้าง query หลัก
    $query = Info::with(['products', 'sellers', 'committeemembers', 'bidders', 'inspectors'])
                 ->orderBy('created_at', 'desc');

    // หากมีการค้นหา ให้เพิ่มเงื่อนไขการค้นหา
    if ($search) {
        $query->where(function ($q) use ($search) {
            // ค้นหาข้อมูลในฟิลด์ของ Info
            $q->where('methode_name', 'LIKE', "%{$search}%")
              ->orWhere('reason_description', 'LIKE', "%{$search}%")
              ->orWhere('office_name', 'LIKE', "%{$search}%")
              ->orWhereHas('products', function ($q) use ($search) {
                  // ค้นหาในฟิลด์ของ Product ที่สัมพันธ์
                  $q->where('product_name', 'LIKE', "%{$search}%")
                    ->orWhere('quantity', 'LIKE', "%{$search}%")
                    ->orWhere('product_price', 'LIKE', "%{$search}%");
              })
              ->orWhereHas('sellers', function ($q) use ($search) {
                  // ค้นหาในฟิลด์ของ Seller ที่สัมพันธ์
                  $q->where('seller_name', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%")
                    ->orWhere('taxpayer_number', 'LIKE', "%{$search}%");
              })
              ->orWhereHas('committeemembers', function ($q) use ($search) {
                  // ค้นหาในฟิลด์ของ CommitteeMember ที่สัมพันธ์
                  $q->where('member_name', 'LIKE', "%{$search}%")
                    ->orWhere('member_position', 'LIKE', "%{$search}%");
              })
              ->orWhereHas('bidders', function ($q) use ($search) {
                  // ค้นหาในฟิลด์ของ Bidder ที่สัมพันธ์
                  $q->where('bidder_name', 'LIKE', "%{$search}%")
                    ->orWhere('bidder_position', 'LIKE', "%{$search}%");
              })
              ->orWhereHas('inspectors', function ($q) use ($search) {
                  // ค้นหาในฟิลด์ของ Inspector ที่สัมพันธ์
                  $q->where('inspector_name', 'LIKE', "%{$search}%")
                    ->orWhere('inspector_position', 'LIKE', "%{$search}%");
              });
        });
    }

    // เพิ่ม appends เพื่อให้ pagination ส่งค่าการค้นหาไปด้วย
    $info = $query->paginate(10)->appends(['search' => $search]);

    return view('page.listpage', compact('info'));
}


    
public function listpdf(Request $request)
{
    $search = $request->query('search');

    // สร้าง query หลัก
    $query = Info::with(['products', 'sellers', 'committeemembers', 'bidders', 'inspectors'])
                 ->where('status', 'Complete') // แสดงเฉพาะข้อมูลที่มีสถานะ Complete
                 ->orderBy('created_at', 'desc');

    // หากมีการค้นหา ให้เพิ่มเงื่อนไขการค้นหา
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('methode_name', 'LIKE', "%{$search}%")
              ->orWhere('reason_description', 'LIKE', "%{$search}%")
              ->orWhere('office_name', 'LIKE', "%{$search}%")
              ->orWhereHas('products', function ($q) use ($search) {
                  // ค้นหาในฟิลด์ของ Product ที่สัมพันธ์
                  $q->where('product_name', 'LIKE', "%{$search}%")
                    ->orWhere('quantity', 'LIKE', "%{$search}%")
                    ->orWhere('product_price', 'LIKE', "%{$search}%");
              })
              ->orWhereHas('sellers', function ($q) use ($search) {
                  // ค้นหาในฟิลด์ของ Seller ที่สัมพันธ์
                  $q->where('seller_name', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%")
                    ->orWhere('taxpayer_number', 'LIKE', "%{$search}%");
              })
              ->orWhereHas('committeemembers', function ($q) use ($search) {
                  // ค้นหาในฟิลด์ของ CommitteeMember ที่สัมพันธ์
                  $q->where('member_name', 'LIKE', "%{$search}%")
                    ->orWhere('member_position', 'LIKE', "%{$search}%");
              })
              ->orWhereHas('bidders', function ($q) use ($search) {
                  // ค้นหาในฟิลด์ของ Bidder ที่สัมพันธ์
                  $q->where('bidder_name', 'LIKE', "%{$search}%")
                    ->orWhere('bidder_position', 'LIKE', "%{$search}%");
              })
              ->orWhereHas('inspectors', function ($q) use ($search) {
                  // ค้นหาในฟิลด์ของ Inspector ที่สัมพันธ์
                  $q->where('inspector_name', 'LIKE', "%{$search}%")
                    ->orWhere('inspector_position', 'LIKE', "%{$search}%");
              });
        });
    }

    // ใช้ appends เพื่อให้ pagination คงค่าการค้นหา
    $info = $query->paginate(10)->appends(['search' => $search]);

    return view('page.listpdf', compact('info'));
}


    
    public function showCreateForm($id = null)
    {
        $info = $id ? Info::with(['products', 'sellers', 'committeemembers', 'bidders', 'inspectors'])->findOrFail($id) : new Info();


        $info->date = $info->date ? Carbon::parse($info->date) : null;

        $info->date_thai = $info->date ? $info->date->translatedFormat('j F Y') : '';
       
        // ดึงข้อมูลผู้ขายทั้งหมดจากฐานข้อมูล
        $allSellers = Seller::all()->unique('seller_name');
        $allBidders = Bidder::all()->unique('bidder_name');
        $allInspectors = Inspector::all()->unique('inspector_name');
        $allCommitteeMembers = CommitteeMember::all()->unique('member_name');
        return view('page.form', compact('info', 'allSellers' , 'allBidders', 'allInspectors', 'allCommitteeMembers'));
    }

    public function showCreateFormk($id = null)
    {
        $info = $id ? Info::with(['products', 'sellers', 'committeemembers', 'bidders', 'inspectors'])->findOrFail($id) : new Info();


        $info->date = $info->date ? Carbon::parse($info->date) : null;

        $info->date_thai = $info->date ? $info->date->translatedFormat('j F Y') : '';
       
         // ดึงข้อมูลผู้ขายทั้งหมดจากฐานข้อมูล
        $allSellers = Seller::all()->unique('seller_name');
        $allBidders = Bidder::all()->unique('bidder_name');
        $allInspectors = Inspector::all()->unique('inspector_name');
        $allCommitteeMembers = CommitteeMember::all()->unique('member_name');

         return view('page.formk', compact('info', 'allSellers' , 'allBidders', 'allInspectors', 'allCommitteeMembers'));
    }

    public function add(Request $request)
{
    $info = new Info();
    $response = $this->save($info, $request);

    // ตรวจสอบการตอบกลับจากฟังก์ชัน save
    if ($response['status'] === 'error') {
        // คืนค่ากลับไปยังหน้าฟอร์มพร้อมกับข้อผิดพลาด
        return redirect()->back()->withErrors($response['message'])->withInput();
    }
    // คำนวณราคาสินค้ารวม
    $totalPrice = 0;
    foreach ($info->products as $product) {
        $totalPrice += $product->quantity * $product->product_price;
    }
    $data['total_price'] = $totalPrice; // เพิ่มยอดรวมใน $data

    // บันทึกกิจกรรมการสร้างข้อมูล
    History::create([
        'user_id' => Auth::id(),
        'activity' => 'สร้างเอกสารใหม่',
        'details' => 'สร้างเอกสารใหม่ ID: ' . $info->id .' /จำนวนเงินที่ใช้ไป: ' . $totalPrice . 'บาท',
    ]);

    return redirect('/page')->with('success', 'ข้อมูลถูกบันทึกเรียบร้อยแล้ว');
}


    public function edit($id = null)
    {
        $info = $id ? Info::with(['products', 'sellers', 'committeemembers', 'bidders', 'inspectors'])->findOrFail($id) : new Info();

        $info->date = $info->date ? Carbon::parse($info->date) : null;

        $info->date_thai = $info->date ? $info->date->translatedFormat('j F Y') : '';
       
        // ดึงข้อมูลผู้ขายทั้งหมดจากฐานข้อมูล
        $allSellers = Seller::all()->unique('seller_name');
        $allBidders = Bidder::all()->unique('bidder_name');
        $allInspectors = Inspector::all()->unique('inspector_name');
        $allCommitteeMembers = CommitteeMember::all()->unique('member_name');
        return view('page.form', compact('info', 'allSellers' , 'allBidders', 'allInspectors', 'allCommitteeMembers'));
    }

    public function editk($id = null)
    {
        $info = $id ? Info::with(['products', 'sellers', 'committeemembers', 'bidders', 'inspectors'])->findOrFail($id) : new Info();

        $info->date = $info->date ? Carbon::parse($info->date) : null;

        $info->date_thai = $info->date ? $info->date->translatedFormat('j F Y') : '';
        
        // ดึงข้อมูลผู้ขายทั้งหมดจากฐานข้อมูล
        $allSellers = Seller::all()->unique('seller_name');
        $allBidders = Bidder::all()->unique('bidder_name');
        $allInspectors = Inspector::all()->unique('inspector_name');
        $allCommitteeMembers = CommitteeMember::all()->unique('member_name');

         return view('page.formk', compact('info', 'allSellers' , 'allBidders', 'allInspectors', 'allCommitteeMembers'));
    }

    public function update(Request $request, $id)
    {
        $info = Info::with(['products', 'sellers', 'committeemembers', 'bidders', 'inspectors'])->findOrFail($id);
        $this->save($info, $request);
    
        // บันทึกกิจกรรมการอัปเดตข้อมูล
        History::create([
            'user_id' => Auth::id(),
            'activity' => 'อัปเดตข้อมูล',
            'details' => 'อัปเดตข้อมูล ID: ' . $info->id,
        ]);
    
        return redirect('/page')->with('success', 'ข้อมูลถูกอัปเดตเรียบร้อยแล้ว');
    }

    private function save($data, $request)
{
    // เริ่มต้นการทำธุรกรรมฐานข้อมูล
    return DB::transaction(function () use ($data, $request) {

        // ดึงข้อมูลผลิตภัณฑ์จากฟอร์ม
        $products = $request->input('products', []);

        // คำนวณรวมราคาผลิตภัณฑ์ทั้งหมด
        $totalPrice = 0;
        if (is_array($products)) {
            foreach ($products as $productData) {
                if (is_array($productData)) {
                    // ลบเครื่องหมาย , ออกจาก product_price ก่อนทำการคำนวณ
                    $productPrice = floatval(str_replace(',', '', $productData['product_price'] ?? 0));
                    $totalPrice += $productPrice; // รวมราคาทั้งหมดของผลิตภัณฑ์
                }
            }
        }

        // ตรวจสอบงบประมาณคงเหลือ
        $budget = Budget::first();

        if (!$budget) {
            return ['status' => 'error', 'message' => 'ไม่พบงบประมาณในระบบ'];
        }

        if ($budget->remaining_amount < $totalPrice) {
            return ['status' => 'error', 'message' => 'งบประมาณไม่เพียงพอ กรุณาปรับราคาใหม่'];
        }

        // หากงบประมาณเพียงพอ ทำการบันทึกข้อมูลและหักจากงบประมาณคงเหลือ
        $budget->remaining_amount -= $totalPrice;
        $budget->save();

    
        // บันทึกข้อมูล Info
        $data->methode_name = $request->input('methode_name', $data->methode_name);
        $data->reason_description = $request->input('reason_description', $data->reason_description);
        $data->office_name = $request->input('office_name', $data->office_name);

        // บันทึกวันที่
        $data->date = $request->input('date') ? Carbon::parse($request->input('date')) : $data->date;

        // บันทึกข้อมูลที่เหลือ
        $data->devilvery_time = $request->input('devilvery_time', $data->devilvery_time);
        $data->template_source = $request->input('template_source', $data->template_source); // บันทึก template_source
        $data->save();

        // บันทึกข้อมูลผลิตภัณฑ์
        if (is_array($products)) {
            foreach ($products as $productData) {
                if (is_array($productData)) {
                    // ลบเครื่องหมาย , ออกจาก product_price ก่อนบันทึกลงฐานข้อมูล
                    $productData['product_price'] = str_replace(',', '', $productData['product_price']);

                    Product::updateOrCreate(
                        ['id' => $productData['id'] ?? null],
                        array_merge($productData, ['info_id' => $data->id])
                    );
                }
            }
        }

        // บันทึกข้อมูลผู้ขาย
        $sellers = $request->input('sellers', []);
        if (is_array($sellers)) {
            foreach ($sellers as $sellerData) {
                if (is_array($sellerData)) {
                    Seller::updateOrCreate(
                        ['id' => $sellerData['id'] ?? null],
                        array_merge($sellerData, ['info_id' => $data->id])
                    );
                }
            }
        }

        // บันทึกข้อมูลคณะกรรมการ
        $committeemembers = $request->input('committeemembers', []);
        if (is_array($committeemembers)) {
            foreach ($committeemembers as $memberData) {
                if (is_array($memberData)) {
                    CommitteeMember::updateOrCreate(
                        ['id' => $memberData['id'] ?? null],
                        array_merge($memberData, ['info_id' => $data->id])
                    );
                }
            }
        }

        // บันทึกข้อมูลผู้เสนอราคา
        $bidders = $request->input('bidders', []);
        if (is_array($bidders)) {
            foreach ($bidders as $bidderData) {
                if (is_array($bidderData)) {
                    Bidder::updateOrCreate(
                        ['id' => $bidderData['id'] ?? null],
                        array_merge($bidderData, ['info_id' => $data->id])
                    );
                }
            }
        }

        // บันทึกข้อมูลผู้ตรวจสอบ
        $inspectors = $request->input('inspectors', []);
        if (is_array($inspectors)) {
            foreach ($inspectors as $inspectorData) {
                if (is_array($inspectorData)) {
                    Inspector::updateOrCreate(
                        ['id' => $inspectorData['id'] ?? null],
                        array_merge($inspectorData, ['info_id' => $data->id])
                    );
                }
            }
        }

        return ['status' => 'success', 'message' => 'ข้อมูลถูกบันทึกเรียบร้อยแล้ว'];
    });
}

public function dashboard()
{
    // Query to get monthly counts for 'จัดซื้อ' and 'จัดจ้าง'
    $monthlyData = DB::table('info')
        ->select(DB::raw('MONTH(created_at) as month, 
                            SUM(CASE WHEN methode_name = "จัดซื้อ" THEN 1 ELSE 0 END) as purchase_count, 
                            SUM(CASE WHEN methode_name = "จัดจ้าง" THEN 1 ELSE 0 END) as hiring_count'))
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->orderBy('month')
        ->get();
    
    // Prepare data for 'จัดซื้อ' and 'จัดจ้าง' chart
    $months = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
    $purchaseData = array_fill(0, 12, 0);
    $hiringData = array_fill(0, 12, 0);
    
    foreach ($monthlyData as $data) {
        $purchaseData[$data->month - 1] = $data->purchase_count;
        $hiringData[$data->month - 1] = $data->hiring_count;
    }

    // Query to get monthly counts for 'วัสดุ' and 'ครุภัณฑ์'
    $productMonthlyData = DB::table('product')
        ->select(DB::raw('MONTH(created_at) as month, 
                            SUM(CASE WHEN product_type = "วัสดุ" THEN quantity ELSE 0 END) as material_count, 
                            SUM(CASE WHEN product_type = "ครุภัณฑ์" THEN quantity ELSE 0 END) as equipment_count'))
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->orderBy('month')
        ->get();
    
    // Prepare data for 'วัสดุ' and 'ครุภัณฑ์' chart
    $materialData = array_fill(0, 12, 0);
    $equipmentData = array_fill(0, 12, 0);
    
    foreach ($productMonthlyData as $data) {
        $materialData[$data->month - 1] = $data->material_count;
        $equipmentData[$data->month - 1] = $data->equipment_count;
    }

    return view('dashboard', compact('months', 'purchaseData', 'hiringData', 'materialData', 'equipmentData'));
}

public function showHistory(Request $request)
{
    $search = $request->query('search');

    // สร้าง query หลักเพื่อดึงข้อมูลกิจกรรมและผู้ใช้
    $query = History::with('user')->orderBy('created_at', 'desc');

    // หากมีการค้นหา ให้เพิ่มเงื่อนไขการค้นหา
    if ($search) {
        $query->where(function ($q) use ($search) {
            // ค้นหาข้อมูลในฟิลด์ของ History และ User
            $q->where('activity', 'LIKE', "%{$search}%")
              ->orWhere('details', 'LIKE', "%{$search}%")
              ->orWhereHas('user', function ($q) use ($search) {
                  // ค้นหาในฟิลด์ของผู้ใช้ (User)
                  $q->where('name', 'LIKE', "%{$search}%");
              });
        });
    }

    // เพิ่ม appends เพื่อให้ pagination ส่งค่าการค้นหาไปด้วย
    $history = $query->paginate(10)->appends(['search' => $search]);

    return view('page.history', compact('history'));
}


    public function someAction()
{
    // ตัวอย่างการบันทึกกิจกรรม
    History::create([
        'user_id' => Auth::id(),
        'activity' => 'Created a new record',
        'details' => 'Record details...',
    ]);

    // ทำงานอื่นๆ เช่น การบันทึกข้อมูลในฟอร์ม
}

public function getRemainingBudget()
{
    $budget = Budget::first();

    // จัดรูปแบบค่าเงินให้อยู่ในรูปแบบมี , คั่น
    if ($budget) {
        $budget->total_amount = number_format($budget->total_amount, 2); // แสดงผลทศนิยม 2 ตำแหน่ง
        $budget->remaining_amount = number_format($budget->remaining_amount, 2); // แสดงผลทศนิยม 2 ตำแหน่ง
    }

    return view('page', ['budget' => $budget]);
}


public function showAddBudgetForm()
{
    return view('budget.add'); // แสดงฟอร์มเพิ่มงบประมาณ
}

public function addBudget(Request $request)
{
    $request->validate([
        'budget_amount' => 'required|numeric|min:0',
    ]);

    $budget = Budget::first();
    if (!$budget) {
        $budget = new Budget();
        $budget->total_amount = 0;
        $budget->remaining_amount = 0;
    }

    // เพิ่มงบประมาณที่ได้รับเข้ากับงบประมาณปัจจุบัน
    $budget->total_amount += $request->input('budget_amount');
    $budget->remaining_amount += $request->input('budget_amount');
    $budget->save();

    // จัดรูปแบบตัวเลขหลังจากเพิ่มงบประมาณแล้ว
    $formattedTotalAmount = number_format($budget->total_amount, 2);
    $formattedRemainingAmount = number_format($budget->remaining_amount, 2);

    // ส่งกลับไปที่หน้าเดิมพร้อมกับข้อความสำเร็จ
    return redirect()->route('budget.add')->with('success', "งบประมาณถูกเพิ่มเรียบร้อยแล้ว งบประมาณทั้งหมด: {$formattedTotalAmount} บาท คงเหลือ: {$formattedRemainingAmount} บาท");
}

public function summarys(Request $request)
{
    // ตัวอย่างข้อมูล
    $monthlySummaries = [
        ['id' => 1, 'title' => 'สรุปการจัดซื้อจัดจ้างประจำเดือนมกราคม 2567'],
        ['id' => 2, 'title' => 'สรุปการจัดซื้อจัดจ้างประจำเดือนกุมภาพันธ์ 2567'],
        ['id' => 3, 'title' => 'สรุปการจัดซื้อจัดจ้างประจำเดือนมีนาคม 2567'],
    ];

    return view('summary', compact('monthlySummaries'));
}




}
