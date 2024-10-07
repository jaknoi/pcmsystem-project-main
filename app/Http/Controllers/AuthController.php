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
use App\Models\More;
use Carbon\Carbon;
use App\Models\History;
use App\Models\Budget;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    public function welcome()
    {
        return view('welcome');
    }

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
        $remember = $request->has('remember'); // ตรวจสอบว่าผู้ใช้เลือก 'จดจำฉัน' หรือไม่
    
        if (Auth::attempt($credentials, $remember)) { // ส่งค่า $remember ไปด้วย
            // Save login activity
            History::create([
                'user_id' => Auth::id(),
                'activity' => 'Login',
                'details' => 'ผู้ใช้เข้าสู่ระบบ',
            ]);
    
            session(['success' => 'ล็อกอินสำเร็จ!']);
            return redirect()->intended('page')->with('success', 'ล็อกอินสำเร็จ!');
        } else {
            return redirect('/login')
                ->withErrors(['email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง'])
                ->withInput(); // เก็บค่า input เดิม
        }
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

    return redirect('login')->with('logout', 'คุณได้ออกจากระบบเรียบร้อยแล้ว!');
}

public function list(Request $request)
{
    $search = $request->query('search');

    // สร้าง query หลัก
    $query = Info::with(['products', 'sellers', 'committeemembers', 'bidders', 'inspectors','mores'])
                 ->orderBy('created_at', 'desc');

    // หากมีการค้นหา ให้เพิ่มเงื่อนไขการค้นหา
    if ($search) {
        $query->where(function ($q) use ($search) {
            // ค้นหาข้อมูลในฟิลด์ของ Info
            $q->where('id', 'LIKE', "%{$search}%")
            ->orWhere('methode_name', 'LIKE', "%{$search}%")
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
    $query = Info::with(['products', 'sellers', 'committeemembers', 'bidders', 'inspectors','mores'])
                 ->where('status', 'Complete') // แสดงเฉพาะข้อมูลที่มีสถานะ Complete
                 ->orderBy('created_at', 'desc');

    // หากมีการค้นหา ให้เพิ่มเงื่อนไขการค้นหา
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('id', 'LIKE', "%{$search}%")
              ->orWhere('methode_name', 'LIKE', "%{$search}%")
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

  

    return view('page.form', compact('info', 'allSellers', 'allBidders', 'allInspectors', 'allCommitteeMembers'));
}



public function showCreateFormk($id = null)
{
    $info = $id ? Info::with(['products', 'sellers', 'committeemembers', 'bidders', 'inspectors', 'mores'])->findOrFail($id) : new Info();

    $info->date = $info->date ? Carbon::parse($info->date) : null;
    $info->date_thai = $info->date ? $info->date->translatedFormat('j F Y') : '';

    // ดึงข้อมูลผู้ขายทั้งหมดจากฐานข้อมูล
    $allSellers = Seller::all()->unique('seller_name');
    $allBidders = Bidder::all()->unique('bidder_name');
    $allInspectors = Inspector::all()->unique('inspector_name');
    $allCommitteeMembers = CommitteeMember::all()->unique('member_name');
    $allMores = More::all();

    

    return view('page.formk', compact('info', 'allSellers', 'allBidders', 'allInspectors', 'allCommitteeMembers', 'allMores'));
}




    public function add(Request $request)
{
    $info = new Info();
    $response = $this->save($info, $request);

    // ตรวจสอบการตอบกลับจากฟังก์ชัน save
    if ($response instanceof \Illuminate\Http\RedirectResponse) {
        // คืนค่ากลับไปยังหน้าฟอร์มพร้อมกับข้อผิดพลาด
        return $response; // คืนค่าตรงๆ เมื่อเป็น RedirectResponse
    }

    // ตรวจสอบสถานะการบันทึก
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
    'details' => 'สร้างเอกสารใหม่ ID: ' . $info->id . ' /จำนวนเงินที่ใช้ไป: ' . number_format($totalPrice) . ' บาท',
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
    // Ensure the 'mores' relation is loaded
    $info = $id ? Info::with(['products', 'sellers', 'committeemembers', 'bidders', 'inspectors', 'mores'])->findOrFail($id) : new Info();

    $info->date = $info->date ? Carbon::parse($info->date) : null;
    $info->date_thai = $info->date ? $info->date->translatedFormat('j F Y') : '';

    $allSellers = Seller::all()->unique('seller_name');
    $allBidders = Bidder::all()->unique('bidder_name');
    $allInspectors = Inspector::all()->unique('inspector_name');
    $allCommitteeMembers = CommitteeMember::all()->unique('member_name');
    $allMores = More::all();
    
    return view('page.formk', compact('info', 'allSellers', 'allBidders', 'allInspectors', 'allCommitteeMembers', 'allMores'));
}

    



    public function update(Request $request, $id)
    {
        $info = Info::with(['products', 'sellers', 'committeemembers', 'bidders', 'inspectors','mores'])->findOrFail($id);
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
                        $quantity = intval($productData['quantity'] ?? 1); // กำหนดค่า quantity หากไม่มี
                        $totalPrice += $quantity * $productPrice; // รวมราคาทั้งหมดของผลิตภัณฑ์
                    }
                }
            }
    

            // ตรวจสอบงบประมาณคงเหลือ
            $budget = Budget::first();
        
            if (!$budget) {
                return redirect()->back()->with('error', 'ไม่พบงบประมาณในระบบ')->withInput();
            }
            
            // ตรวจสอบว่างบประมาณเพียงพอหรือไม่
            if ($budget->remaining_amount < $totalPrice) {
                return redirect()->back()->with('error', 'งบประมาณไม่เพียงพอ กรุณาปรับราคาใหม่')->withInput();
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
            $data->template_source = $request->input('template_source', $data->template_source);
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
    
           // ดึงข้อมูลผู้ขายจากฟอร์ม
        $sellers = $request->input('sellers', []);
        if (is_array($sellers)) {
            foreach ($sellers as $index => $sellerData) {
                if (is_array($sellerData)) {
                    // ตรวจสอบและอัปโหลดไฟล์ PDF ใหม่
                    if ($request->hasFile("sellers.$index.pdf_file")) {
                        $pdfFile = $request->file("sellers.$index.pdf_file");

                        // ดึงชื่อไฟล์เดิม
                        $originalFileName = $pdfFile->getClientOriginalName();

                        // จัดเก็บไฟล์ด้วยชื่อไฟล์เดิมในโฟลเดอร์ 'pdfs' ภายใต้ 'public'
                        $pdfPath = $pdfFile->storeAs('pdfs', $originalFileName, 'public');
                    } else {
                        // หากไม่มีการอัปโหลดใหม่ ให้ใช้ไฟล์ PDF เดิมจากฟิลด์ซ่อน
                        $pdfPath = $sellerData['pdf_file'] ?? null;
                    }

                    // บันทึกหรืออัปเดตข้อมูลผู้ขาย
                    Seller::updateOrCreate(
                        ['id' => $sellerData['id'] ?? null],
                        array_merge($sellerData, [
                            'info_id' => $data->id,
                            'pdf_file' => $pdfPath // ใช้ไฟล์ PDF ที่อัปโหลดใหม่หรือไฟล์เดิม
                        ])
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
    
            // บันทึกข้อมูล More
            $mores = $request->input('mores', []);
            if (is_array($mores)) {
                foreach ($mores as $moreData) {
                    More::updateOrCreate(
                        ['id' => $moreData['id'] ?? null],
                        array_merge($moreData, ['info_id' => $data->id])
                    );
                }
            }
    
            return ['status' => 'success', 'message' => 'ข้อมูลถูกบันทึกเรียบร้อยแล้ว'];
        });
    }
    

    
    

    public function dashboard()
    {
        // Query to get monthly counts for 'จัดซื้อ' and 'จัดจ้าง' starting from October
        $monthlyData = DB::table('info')
            ->select(DB::raw('MONTH(date) as month, 
                                SUM(CASE WHEN methode_name = "จัดซื้อ" THEN 1 ELSE 0 END) as purchase_count, 
                                SUM(CASE WHEN methode_name = "จัดจ้าง" THEN 1 ELSE 0 END) as hiring_count'))
            ->whereYear('date', date('Y') - 1) // Adjust this for fiscal year starting October
            ->orWhere(function ($query) {
                $query->whereYear('date', date('Y'))
                      ->whereMonth('date', '>=', 10); // Select data from October of the previous year
            })
            ->groupBy(DB::raw('MONTH(date)'))
            ->orderBy('month')
            ->get();
        
        // Adjust the months to start from October
        $months = ['ตุลาคม', 'พฤศจิกายน', 'ธันวาคม', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน'];
    
        // Initialize the data arrays starting from October
        $purchaseData = array_fill(0, 12, 0);
        $hiringData = array_fill(0, 12, 0);
    
        // Map the database result to the correct fiscal year month index
        foreach ($monthlyData as $data) {
            $monthIndex = ($data->month + 2) % 12; // Shift months to start from October
            $purchaseData[$monthIndex] = $data->purchase_count;
            $hiringData[$monthIndex] = $data->hiring_count;
        }
    
        // Query to get monthly counts for 'วัสดุ' and 'ครุภัณฑ์'
        $productMonthlyData = DB::table('product')
            ->select(DB::raw('MONTH(created_at) as month, 
                                SUM(CASE WHEN product_type = "วัสดุ" THEN quantity ELSE 0 END) as material_count, 
                                SUM(CASE WHEN product_type = "ครุภัณฑ์" THEN quantity ELSE 0 END) as equipment_count'))
            ->whereYear('created_at', date('Y') - 1) // Adjust for fiscal year starting October
            ->orWhere(function ($query) {
                $query->whereYear('created_at', date('Y'))
                      ->whereMonth('created_at', '>=', 10); // Select data from October
            })
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();
    
        // Initialize the data arrays for products starting from October
        $materialData = array_fill(0, 12, 0);
        $equipmentData = array_fill(0, 12, 0);
    
        // Map the product data to the correct fiscal year month index
        foreach ($productMonthlyData as $data) {
            $monthIndex = ($data->month + 2) % 12; // Shift months to start from October
            $materialData[$monthIndex] = $data->material_count;
            $equipmentData[$monthIndex] = $data->equipment_count;
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
    // ค้นหางบประมาณปัจจุบัน
    $budget = Budget::first();

    // หากไม่มีงบประมาณในฐานข้อมูล ให้ตั้งค่าเป็น 0
    if (!$budget) {
        $budget = new Budget();
        $budget->total_amount = 0;
        $budget->remaining_amount = 0;
        $budget->save(); // บันทึกงบประมาณใหม่
    }

    return view('budget.add', compact('budget')); // ส่งงบประมาณไปยังวิว
}

public function addBudget(Request $request)
{
    $request->validate([
        'budget_amount' => 'required|numeric|min:0',
    ]);

    // ค้นหางบประมาณปัจจุบัน
    $budget = Budget::first();
    
    // หากไม่มีงบประมาณในฐานข้อมูล ให้สร้างใหม่และตั้งค่าเป็น 0
    if (!$budget) {
        $budget = new Budget();
        $budget->total_amount = 0; // ตั้งค่าเป็น 0
        $budget->remaining_amount = 0; // ตั้งค่าเป็น 0
        $budget->save(); // บันทึกก่อนเข้าถึง
    }

    // เพิ่มงบประมาณที่ได้รับเข้ากับงบประมาณปัจจุบัน
    $budget->addBudget($request->input('budget_amount'));

    // บันทึกประวัติการเพิ่มงบประมาณ
    History::create([
        'user_id' => Auth::id(),
        'activity' => 'เพิ่มงบประมาณ',
        'details' => 'เพิ่มงบประมาณจำนวนเงิน ' . number_format($request->input('budget_amount'), 2) . ' บาท',
    ]);

    // จัดรูปแบบตัวเลขหลังจากเพิ่มงบประมาณแล้ว
    $formattedTotalAmount = number_format($budget->total_amount, 2);
    $formattedRemainingAmount = number_format($budget->remaining_amount, 2);

    // ส่งกลับไปที่หน้าเดิมพร้อมกับข้อความสำเร็จ
    return redirect()->route('budget.add')->with('success', "งบประมาณถูกเพิ่มเรียบร้อยแล้ว งบประมาณคงเหลือ: {$formattedRemainingAmount} บาท");
}



}