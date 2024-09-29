<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Info;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use TCPDF;
use Dompdf\Dompdf;
use Dompdf\Options;
use Carbon\Carbon;
use App\Models\History;
use Illuminate\Support\Facades\Auth;

class PdfController extends Controller
{
    // ฟังก์ชันสร้าง PDF จากข้อมูลในฐานข้อมูล
    public function generatePdf($id)
    {
        $info = Info::with(['sellers', 'products', 'committeemembers', 'bidders', 'inspectors'])->findOrFail($id);
        $data = $this->prepareData($info);

        // ตรวจสอบแหล่งที่มาของเทมเพลต
        if ($info->template_source === 'formk') {
            $templatePath = public_path('pcmk.docx'); // ใช้เทมเพลต pcmk.docx สำหรับ formk
        } else {
            $templatePath = public_path('pcm.docx'); // ใช้เทมเพลต pcm.docx สำหรับ form
        }

        $filledDocumentPath = $this->fillWordTemplate($templatePath, $data);
        $pdfPath = $this->convertWordToPdf($filledDocumentPath); // แปลงเป็น PDF โดยตรง

        return response()->download($pdfPath)->deleteFileAfterSend(true);
    }

   // ฟังก์ชันแปลง Word เป็น PDF โดยใช้ Dompdf
private function convertWordToPdf($wordPath)
{
    // โหลดไฟล์ Word ด้วย PhpWord
    $phpWord = IOFactory::load($wordPath);

    // แปลงเป็น HTML
    $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
    ob_start();
    $htmlWriter->save('php://output');
    $htmlContent = ob_get_clean();

    // สร้าง PDF ด้วย TCPDF
    $pdf = new TCPDF();
    $pdf->AddPage();

    // ตั้งค่าฟอนต์ (ถ้าต้องการ)
    $pdf->SetFont('thsarabunit', '', 16);

    // เขียน HTML ลงใน PDF
    $pdf->writeHTML($htmlContent, true, false, true, false, '');

    // บันทึก PDF
    $pdfPath = public_path('pcm-filled.pdf');
    $pdf->Output($pdfPath, 'F');

    return $pdfPath;
}


    // ฟังก์ชันเติมข้อมูลลงในเทมเพลตเอกสาร Word
private function fillWordTemplate($templatePath, $data)
{
    $templateProcessor = new TemplateProcessor($templatePath);

    // เติมข้อมูลลงในเทมเพลตสำหรับฟิลด์ธรรมดา
    $templateProcessor->setValue('methode_name', $data['methode_name']);

    // แปลงวันที่เป็นรูปแบบ วัน เดือน ปี ภาษาไทย
    if (!empty($data['date'])) {
        // แปลงวันที่เป็น Timestamp
        $timestamp = strtotime($data['date']);
        
        // กำหนดรูปแบบวันที่เป็น วัน เดือน ปี ภาษาไทย
        $thaiDate = date('j', $timestamp) . ' ' . $this->getThaiMonth(date('n', $timestamp)) . ' ' . (date('Y', $timestamp) + 543); // เพิ่ม 543 เพื่อแปลงเป็นปีไทย
    } else {
        $thaiDate = '';
    }

    // เติมวันที่ลงในเทมเพลต
    $templateProcessor->setValue('date', $thaiDate);
    
    $templateProcessor->setValue('reason_description', $data['reason_description']);
    $templateProcessor->setValue('office_name', $data['office_name']);
    $templateProcessor->setValue('devilvery_time', $data['devilvery_time']);

    // เติมยอดรวม (totalPrice)
    $templateProcessor->setValue('total_price', number_format($data['total_price'], 2)); // กำหนดให้แสดงผลเป็นตัวเลข 2 ตำแหน่งหลังจุดทศนิยม
// เติมยอดรวมในคำอ่านตัวหนังสือ
$templateProcessor->setValue('total_price_text', $this->convertNumberToThaiText($data['total_price']));

    // เติมข้อมูล sellers แต่ละรายการโดยไม่ใช้ cloneRow
    if (isset($data['sellers'])) {
        foreach ($data['sellers'] as $index => $seller) {
            $templateProcessor->setValue("seller_name#" . ($index + 1), $seller['seller_name']);
            $templateProcessor->setValue("address#" . ($index + 1), $seller['address']);
            $templateProcessor->setValue("taxpayer_number#" . ($index + 1), $seller['taxpayer_number']);
            $templateProcessor->setValue("reference_documents#" . ($index + 1), $seller['reference_documents']);
        }
    }

    if (isset($data['products'])) {
        // เติมข้อมูลสินค้าลงในตารางโดยใช้ cloneRow
        $templateProcessor->cloneRow('product_name', count($data['products']));
        foreach ($data['products'] as $index => $product) {
            // เติมข้อมูลในตาราง
            $templateProcessor->setValue("item_number#" . ($index + 1), $index + 1);
            $templateProcessor->setValue("product_name#" . ($index + 1), $product['product_name']);
            $templateProcessor->setValue("quantity#" . ($index + 1), $product['quantity']);
            $templateProcessor->setValue("unit#" . ($index + 1), $product['unit']);
            
            // จัดรูปแบบราคาด้วย number_format
            $formatted_price = number_format($product['product_price'], 2); // 2 คือจำนวนตำแหน่งทศนิยม
            $templateProcessor->setValue("product_price#" . ($index + 1), $formatted_price);
        }
        
        // เติมข้อมูลสินค้าที่อยู่ด้านนอกตาราง (แค่เลขหน้าและชื่อสินค้า)
        $productDetailsOutside = ''; // สร้างข้อความเปล่าเพื่อเก็บรายละเอียดสินค้าที่อยู่นอกตาราง
        foreach ($data['products'] as $index => $product) {
            // สร้างข้อความเฉพาะเลขหน้าและชื่อสินค้า
            $productDetailsOutside .= ($index + 1) . ' ' . $product['product_name'] . "\n";
        }
        
        // เติมข้อมูลลงใน Placeholder เดียว (นอกตาราง)
        $templateProcessor->setValue('product_details_outside', $productDetailsOutside);
    }

    // เติมข้อมูล committeemembers แต่ละรายการ
    if (isset($data['committeemembers'])) {
        foreach ($data['committeemembers'] as $index => $member) {
            $templateProcessor->setValue("member_name#" . ($index + 1), $member['member_name']);
            $templateProcessor->setValue("member_position#" . ($index + 1), $member['member_position']);
        }
    }

    // เติมข้อมูล bidders แต่ละรายการ
    if (isset($data['bidders'])) {
        foreach ($data['bidders'] as $index => $bidder) {
            $templateProcessor->setValue("bidder_name#" . ($index + 1), $bidder['bidder_name']);
            $templateProcessor->setValue("bidder_position#" . ($index + 1), $bidder['bidder_position']);
        }
    }

    // เติมข้อมูล inspectors แต่ละรายการ
    if (isset($data['inspectors'])) {
        foreach ($data['inspectors'] as $index => $inspector) {
            $templateProcessor->setValue("inspector_name#" . ($index + 1), $inspector['inspector_name']);
            $templateProcessor->setValue("inspector_position#" . ($index + 1), $inspector['inspector_position']);
        }
    }

    // บันทึกไฟล์เอกสารใหม่ที่เติมข้อมูลแล้ว
    $outputPath = public_path('pcm-filled.docx');
    $templateProcessor->saveAs($outputPath);

    return $outputPath;
}

// ฟังก์ชันเพื่อแปลงหมายเลขเดือนเป็นชื่อเดือนภาษาไทย
private function getThaiMonth($month)
{
    $thaiMonths = [
        1 => 'มกราคม',
        2 => 'กุมภาพันธ์',
        3 => 'มีนาคม',
        4 => 'เมษายน',
        5 => 'พฤษภาคม',
        6 => 'มิถุนายน',
        7 => 'กรกฎาคม',
        8 => 'สิงหาคม',
        9 => 'กันยายน',
        10 => 'ตุลาคม',
        11 => 'พฤศจิกายน',
        12 => 'ธันวาคม',
    ];

    return isset($thaiMonths[$month]) ? $thaiMonths[$month] : '';
}
// ฟังก์ชันสำหรับแปลงจำนวนเงินเป็นคำอ่านตัวหนังสือภาษาไทย
private function convertNumberToThaiText($number)
{
    $units = ['', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน'];
    $digits = ['', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า'];
    $bahtText = '';

    if ($number == 0) {
        return 'ศูนย์บาทถ้วน';
    }

    // แยกจำนวนเต็มและทศนิยม
    $parts = explode('.', number_format($number, 2, '.', ''));
    $integerPart = $parts[0]; // จำนวนเต็ม
    $decimalPart = isset($parts[1]) ? $parts[1] : '00'; // ทศนิยม

    // แปลงส่วนจำนวนเต็ม
    $length = strlen($integerPart);
    for ($i = 0; $i < $length; $i++) {
        $digit = $integerPart[$i];
        if ($digit != 0) {
            if ($digit == 1 && $i == ($length - 1)) {
                $bahtText .= 'เอ็ด';
            } elseif ($digit == 1 && $i == ($length - 2)) {
                $bahtText .= '';
            } elseif ($digit == 2 && $i == ($length - 2)) {
                $bahtText .= 'ยี่';
            } else {
                $bahtText .= $digits[$digit];
            }
            $bahtText .= $units[$length - $i - 1];
        }
    }
    $bahtText .= 'บาท';

    // แปลงส่วนทศนิยม
    if ($decimalPart == '00') {
        $bahtText .= 'ถ้วน';
    } else {
        $bahtText .= $this->convertDecimalToThaiText($decimalPart);
    }

    return $bahtText;
}

// ฟังก์ชันสำหรับแปลงทศนิยมเป็นคำอ่านตัวหนังสือภาษาไทย
private function convertDecimalToThaiText($decimalPart)
{
    $digits = ['', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า'];
    $decimalText = '';

    if ($decimalPart[0] == 0 && $decimalPart[1] == 0) {
        return 'ถ้วน';
    }

    if ($decimalPart[0] != 0) {
        if ($decimalPart[0] == 1) {
            $decimalText .= 'สิบ';
        } elseif ($decimalPart[0] == 2) {
            $decimalText .= 'ยี่สิบ';
        } else {
            $decimalText .= $digits[$decimalPart[0]] . 'สิบ';
        }
    }

    if ($decimalPart[1] != 0) {
        if ($decimalPart[1] == 1) {
            $decimalText .= 'เอ็ดสตางค์';
        } else {
            $decimalText .= $digits[$decimalPart[1]] . 'สตางค์';
        }
    }

    return $decimalText;
}



    // ฟังก์ชันเตรียมข้อมูลจากฐานข้อมูล
    private function prepareData($info)
    {
        return [
            'methode_name' => $info->methode_name,
            'date' => $this->formatDate($info->date),
            'reason_description' => $info->reason_description,
            'office_name' => $info->office_name,
            'devilvery_time' => $info->devilvery_time,
            'sellers' => $info->sellers->map(function ($seller) {
                return [
                    'seller_name' => $seller->seller_name,
                    'address' => $seller->address,
                    'taxpayer_number' => $seller->taxpayer_number,
                    'reference_documents' => $seller->reference_documents,
                ];
            })->toArray(),
            'products' => $info->products->map(function ($product) {
                return [
                    'product_name' => $product->product_name,
                    'quantity' => $product->quantity,
                    'unit' => $product->unit,
                    'product_price' => $product->product_price,
                ];
            })->toArray(),
            'committeemembers' => $info->committeemembers->map(function ($member) {
                return [
                    'member_name' => $member->member_name,
                    'member_position' => $member->member_position,
                ];
            })->toArray(),
            'bidders' => $info->bidders->map(function ($bidder) {
                return [
                    'bidder_name' => $bidder->bidder_name,
                    'bidder_position' => $bidder->bidder_position,
                ];
            })->toArray(),
            'inspectors' => $info->inspectors->map(function ($inspector) {
                return [
                    'inspector_name' => $inspector->inspector_name,
                    'inspector_position' => $inspector->inspector_position,
                ];
            })->toArray(),
        ];
    }

    private function formatDate($date)
    {
        if ($date instanceof \Carbon\Carbon) {
            return $date->format('Y-m-d');
        }

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return $date; // คืนค่าเดิมถ้าไม่สามารถแปลงได้
        }
    }

    public function previewPdf($id)
{
    // ดึงข้อมูลพร้อมความสัมพันธ์ที่จำเป็น
    $info = Info::with(['sellers', 'products', 'committeemembers', 'bidders', 'inspectors'])->findOrFail($id);
    $data = $this->prepareData($info);

    // ตรวจสอบแหล่งที่มาของเทมเพลต
    $templatePath = $info->template_source === 'formk' ? public_path('pcmk.docx') : public_path('pcm.docx');

    // ใช้ PhpWord เพื่อโหลดเทมเพลต Word
    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

    // เติมข้อมูลลงในเทมเพลต Word
    $templateProcessor->setValue('methode_name', $data['methode_name']);
    $templateProcessor->setValue('date', $data['date'] ?? '');
    $templateProcessor->setValue('reason_description', $data['reason_description']);
    $templateProcessor->setValue('office_name', $data['office_name']);
    $templateProcessor->setValue('devilery_time', $data['devilvery_time']);
    
    // เติมข้อมูล Sellers
    if (!empty($data['sellers'])) {
        foreach ($data['sellers'] as $index => $seller) {
            $templateProcessor->setValue("seller_name#" . ($index + 1), $seller['seller_name']);
            $templateProcessor->setValue("address#" . ($index + 1), $seller['address']);
            $templateProcessor->setValue("taxpayer_number#" . ($index + 1), $seller['taxpayer_number']);
            $templateProcessor->setValue("reference_documents#" . ($index + 1), $seller['reference_documents']);
        }
    }

    // เติมข้อมูล Products
    if (!empty($data['products'])) {
        foreach ($data['products'] as $index => $product) {
            $templateProcessor->setValue("product_name#" . ($index + 1), $product['product_name']);
            $templateProcessor->setValue("quantity#" . ($index + 1), $product['quantity']);
            $templateProcessor->setValue("unit#" . ($index + 1), $product['unit']);
            $templateProcessor->setValue("product_price#" . ($index + 1), $product['product_price']);
        }
    }

    // เติมข้อมูล Committee Members
    if (!empty($data['committeemembers'])) {
        foreach ($data['committeemembers'] as $index => $member) {
            $templateProcessor->setValue("member_name#" . ($index + 1), $member['member_name']);
            $templateProcessor->setValue("member_position#" . ($index + 1), $member['member_position']);
        }
    }

    // เติมข้อมูล Bidders
    if (!empty($data['bidders'])) {
        foreach ($data['bidders'] as $index => $bidder) {
            $templateProcessor->setValue("bidder_name#" . ($index + 1), $bidder['bidder_name']);
            $templateProcessor->setValue("bidder_position#" . ($index + 1), $bidder['bidder_position']);
        }
    }

    // เติมข้อมูล Inspectors
    if (!empty($data['inspectors'])) {
        foreach ($data['inspectors'] as $index => $inspector) {
            $templateProcessor->setValue("inspector_name#" . ($index + 1), $inspector['inspector_name']);
            $templateProcessor->setValue("inspector_position#" . ($index + 1), $inspector['inspector_position']);
        }
    }

    // บันทึกเอกสาร Word ที่มีการเติมข้อมูลแล้ว
    $outputWordPath = storage_path('app/public/filled_template.docx');
    $templateProcessor->saveAs($outputWordPath);

    // แปลงเอกสาร Word เป็น HTML
    $phpWord = \PhpOffice\PhpWord\IOFactory::load($outputWordPath);
    $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');

    ob_start();
    $htmlWriter->save('php://output');
    $htmlContent = ob_get_clean();

    // ตรวจสอบว่ามีข้อมูล HTML สำหรับสร้าง PDF หรือไม่
    if (empty($htmlContent)) {
        return redirect()->back()->with('error', 'ไม่สามารถสร้างเนื้อหา PDF ได้');
    }

    // สร้าง PDF โดยใช้ TCPDF
    $pdf = new \TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('เอกสารจัดซื้อจัดจ้าง');
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->AddPage();

    // ตั้งค่าฟอนต์
    $pdf->SetFont('thsarabunit', '', 16);

    // เขียน HTML ลงใน PDF
    $pdf->writeHTML($htmlContent, true, false, true, false, '');

    // บันทึก PDF
    $pdfPath = storage_path('app/public/preview.pdf');
    $pdf->Output($pdfPath, 'F');

    // ส่งไฟล์ PDF ให้กับผู้ใช้
    return response()->file($pdfPath, [
        'Content-Type' => 'application/pdf',
    ])->deleteFileAfterSend(true);
}

    
    

    public function generateWord($id)
{
    $info = Info::findOrFail($id);

    // ตรวจสอบแหล่งที่มาของเทมเพลต
    if ($info->template_source === 'formk') {
        $templatePath = public_path('pcmk.docx'); // ใช้เทมเพลต pcmk.docx สำหรับ formk
    } else {
        $templatePath = public_path('pcm.docx'); // ใช้เทมเพลต pcm.docx สำหรับ form
    }

    // เตรียมข้อมูลที่จะใช้เติมลงในเทมเพลต
    $data = $this->prepareData($info);
    
    // คำนวณราคาสินค้ารวม
    $totalPrice = 0;
    foreach ($info->products as $product) {
        $totalPrice += $product->quantity * $product->product_price;
    }
    $data['total_price'] = $totalPrice; // เพิ่มยอดรวมใน $data

    // เติมข้อมูลลงในเทมเพลต
    $filledDocumentPath = $this->fillWordTemplate($templatePath, $data);

    // บันทึกประวัติการดาวน์โหลด
    History::create([
        'user_id' => Auth::id(), // บันทึกผู้ใช้งานที่ทำการดาวน์โหลด
        'activity' => 'ดาวน์โหลด Word', // กิจกรรมที่ทำ
        'details' => 'ดาวน์โหลด Word ID: ' . $info->id  , // รายละเอียดกิจกรรม
    ]);

    // ตรวจสอบว่าไฟล์ที่บันทึกเสร็จแล้วมีอยู่จริง
    if (file_exists($filledDocumentPath)) {
        return response()->download($filledDocumentPath, 'pcm-filled.docx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="pcm-filled.docx"',
        ])->deleteFileAfterSend(true);
    } else {
        return redirect()->back()->with('error', 'ไม่สามารถดาวน์โหลดไฟล์ได้');
    }
}



    // ฟังก์ชันแสดงหน้าการยืนยันการสร้าง PDF
    public function showConfirmation($id)
    {
        $info = Info::findOrFail($id);
        return view('page.confirm', compact('info'));
    }

    public function confirmPdfGeneration(Request $request, $id)
    {
        // ดึงข้อมูลจากฐานข้อมูล
        $info = Info::findOrFail($id);
    
        // เตรียมข้อมูลที่จะใช้เติมลงในเทมเพลต Word
        $data = [
            'methode_name' => $info->methode_name,
            'date' => $this->formatDate($info->date),
            'reason_description' => $info->reason_description,
            'office_name' => $info->office_name,
            'devilvery_time' => $info->devilvery_time,
        ];
    
        // สร้างเอกสาร PDF โดยใช้ TCPDF
        $pdf = new \TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('ระบบจัดซื้อจัดจ้าง');
        $pdf->SetTitle('เอกสารจัดซื้อจัดจ้าง');
        $pdf->SetHeaderData('', 0, 'เอกสารจัดซื้อจัดจ้าง', '');
        $pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
        $pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    
        // เพิ่มหน้าใหม่ใน PDF
        $pdf->AddPage();
    
        // ตั้งค่าฟอนต์ THSarabunIT๙ (ตรวจสอบให้แน่ใจว่าได้แปลงฟอนต์แล้ว)
        $pdf->SetFont('thsarabunit', '', 16);
    
        // สร้างเนื้อหาใน PDF จากข้อมูลที่ดึงมา
        $htmlContent = '
            <h1>ข้อมูลจัดซื้อจัดจ้าง</h1>
            <p><strong>ประเภท:</strong> ' . $data['methode_name'] . '</p>
            <p><strong>วันที่:</strong> ' . $data['date'] . '</p>
            <p><strong>เหตุผล:</strong> ' . $data['reason_description'] . '</p>
            <p><strong>คณะ:</strong> ' . $data['office_name'] . '</p>
            <p><strong>ระยะเวลาแล้วเสร็จ:</strong> ' . $data['devilvery_time'] . '</p>
            <h2>ผู้ขาย</h2>';
    
        foreach ($info->sellers as $seller) {
            $htmlContent .= '<p>- ' . $seller->seller_name . ' (' . $seller->address . '), หมายเลขผู้เสียภาษี: ' . $seller->taxpayer_number . ', เอกสารอ้างอิง: ' . $seller->reference_documents . '</p>';
        }
    
        $htmlContent .= '<h2>ผลิตภัณฑ์</h2>';
        foreach ($info->products as $product) {
            $htmlContent .= '<p>- ' . $product->product_name . ' จำนวน: ' . $product->quantity . ' หน่วย: ' . $product->unit . ' ราคา: ' . $product->product_price . '</p>';
        }
    
        // เขียนเนื้อหาลงใน PDF
        $pdf->writeHTML($htmlContent, true, false, true, false, '');
    
        // บันทึก PDF ที่สร้างแล้ว
        $pdfPath = storage_path('app/public/preview.pdf');
        $pdf->Output($pdfPath, 'F');
    
        // อัปเดตสถานะข้อมูล
        $info->status = 'Complete';
        $info->save();
    
        // Redirect ไปยังหน้า list และแสดงข้อความสำเร็จ
        return redirect()->route('page.listpdf')->with('success', 'สร้าง PDF สำเร็จ!');
    }
    
    



public function storeForm(Request $request)
{
    $info = new Info();
    $info->template_source = 'form'; // กำหนดแหล่งที่มาเป็น form
    $this->save($info, $request);
    return redirect('/page')->with('success', 'ข้อมูลถูกบันทึกเรียบร้อยแล้ว');
}

public function storeFormk(Request $request)
{
    $info = new Info();
    $info->template_source = 'formk'; // กำหนดแหล่งที่มาเป็น formk
    $this->save($info, $request);
    return redirect('/page')->with('success', 'ข้อมูลถูกบันทึกเรียบร้อยแล้ว');
}
}

