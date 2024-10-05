<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
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
use Illuminate\Support\Facades\App;
use PDF;

class PdfController extends Controller
{
    // ฟังก์ชันสร้าง PDF จากข้อมูลในฐานข้อมูล
    public function generatePdf($id)
{
    $info = Info::with(['sellers', 'products', 'committeemembers', 'bidders', 'inspectors'])->findOrFail($id);
    $data = $this->prepareData($info);

    // เริ่มต้นการสร้าง PDF ด้วย TCPDF
    $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('ระบบจัดซื้อจัดจ้าง');
    $pdf->SetTitle('เอกสารจัดซื้อจัดจ้าง');
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->AddPage();

    // ตั้งค่าฟอนต์ THSarabunIT๙
    $pdf->SetFont('thsarabunit', '', 16);

    // สร้างเนื้อหาจากข้อมูลที่ดึงมา
    $htmlContent = '
        <h1>ข้อมูลจัดซื้อจัดจ้าง</h1>
        <p><strong>ประเภท:</strong> ' . $data['methode_name'] . '</p>
        <p><strong>วันที่:</strong> ' . $data['date'] . '</p>
        <p><strong>เหตุผล:</strong> ' . $data['reason_description'] . '</p>
        <p><strong>คณะ:</strong> ' . $data['office_name'] . '</p>
        <p><strong>ระยะเวลาแล้วเสร็จ:</strong> ' . $data['devilvery_time'] . '</p>
        <h2>ผู้ขาย</h2>';

    // ใส่ข้อมูลผู้ขาย
    foreach ($data['sellers'] as $seller) {
        $htmlContent .= '<p>- ' . $seller['seller_name'] . ' (' . $seller['address'] . '), หมายเลขผู้เสียภาษี: ' . $seller['taxpayer_number'] . '</p>';
    }

    $htmlContent .= '<h2>ผลิตภัณฑ์</h2>';
    foreach ($data['products'] as $product) {
        $htmlContent .= '<p>- ' . $product['product_name'] . ' จำนวน: ' . $product['quantity'] . ' หน่วย: ' . $product['unit'] . ' ราคา: ' . number_format($product['product_price'], 2) . '</p>';
    }

    // ใส่ข้อมูลคณะกรรมการ
    $htmlContent .= '<h2>คณะกรรมการ</h2>';
    foreach ($data['committeemembers'] as $member) {
        $htmlContent .= '<p>- ' . $member['member_name'] . ' ตำแหน่ง: ' . $member['member_position'] . '</p>';
    }

    // ใส่ข้อมูลผู้เสนอราคา
    $htmlContent .= '<h2>ผู้เสนอราคา</h2>';
    foreach ($data['bidders'] as $bidder) {
        $htmlContent .= '<p>- ' . $bidder['bidder_name'] . ' ตำแหน่ง: ' . $bidder['bidder_position'] . '</p>';
    }

    // ใส่ข้อมูลผู้ตรวจสอบ
    $htmlContent .= '<h2>ผู้ตรวจสอบ</h2>';
    foreach ($data['inspectors'] as $inspector) {
        $htmlContent .= '<p>- ' . $inspector['inspector_name'] . ' ตำแหน่ง: ' . $inspector['inspector_position'] . '</p>';
    }

    // เขียน HTML ลงใน PDF
    $pdf->writeHTML($htmlContent, true, false, true, false, '');

    // บันทึก PDF
    $pdfPath = public_path('pcm-filled.pdf');
    $pdf->Output($pdfPath, 'F');

    return response()->download($pdfPath)->deleteFileAfterSend(true);
}

// ฟังก์ชันแปลงไฟล์ Word เป็น PDF
private function convertWordToPdf($wordPath, $outputDir, $documentId)
{
    // ตรวจสอบว่ามีไฟล์ Word อยู่จริง
    if (!file_exists($wordPath)) {
        throw new \Exception('Word file does not exist: ' . $wordPath);
    }

    // ตรวจสอบว่า LibreOffice ถูกติดตั้งอยู่ในตำแหน่งที่ถูกต้อง
    $libreOfficePath = 'C:/Program Files/LibreOffice/program/soffice.exe';
    if (!file_exists($libreOfficePath)) {
        throw new \Exception('LibreOffice not found at path: ' . $libreOfficePath);
    }

    // กำหนดชื่อไฟล์ PDF ที่ต้องการ (ตั้งเป็น "เอกสารจัดซื้อจัดจ้าง_ID.pdf")
    $outputFileName = 'เอกสารจัดซื้อจัดจ้าง_' . $documentId . '.pdf';
    
    // สร้างคำสั่งสำหรับแปลงไฟล์
    $command = '"' . $libreOfficePath . '" --headless --convert-to pdf "' . $wordPath . '" --outdir "' . $outputDir . '"';
    
    // ใช้คำสั่ง exec() เพื่อรันคำสั่ง
    exec($command, $output, $resultCode);

    // ตรวจสอบว่าไฟล์ PDF ถูกสร้างขึ้นสำเร็จ
    if ($resultCode !== 0) {
        throw new \Exception('Failed to convert Word to PDF. Command: ' . $command . "\nOutput: " . implode("\n", $output));
    }

    // ระบุชื่อไฟล์ PDF ที่ถูกสร้าง
    $generatedPdfPath = $outputDir . '/' . pathinfo($wordPath, PATHINFO_FILENAME) . '.pdf';
    
    // กำหนดเส้นทางใหม่สำหรับเปลี่ยนชื่อไฟล์เป็น "เอกสารจัดซื้อจัดจ้าง_ID.pdf"
    $pdfFilePath = $outputDir . '/' . $outputFileName;

    // เปลี่ยนชื่อไฟล์ PDF เป็นชื่อที่ต้องการ
    if (!rename($generatedPdfPath, $pdfFilePath)) {
        throw new \Exception('Failed to rename PDF to: ' . $pdfFilePath);
    }

    return $pdfFilePath;
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

    // เติมยอดรวม (deposit_price)
    $templateProcessor->setValue('deposit_price', number_format($data['deposit_price'], 2)); // กำหนดให้แสดงผลเป็นตัวเลข 2 ตำแหน่งหลังจุดทศนิยม
    // เติมยอดรวมในคำอ่านตัวหนังสือ
    $templateProcessor->setValue('deposit_price_text', $this->convertNumberToThaiText($data['deposit_price']));

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

    // เติมข้อมูล inspectors แต่ละรายการ
    if (isset($data['mores'])) {
        foreach ($data['mores'] as $index => $more) {
            $templateProcessor->setValue("price_list#" . ($index + 1), $more['price_list']);
            $templateProcessor->setValue("request_documents#" . ($index + 1), $more['request_documents']);
            $templateProcessor->setValue("middle_price_first#" . ($index + 1), $more['middle_price_first']);
            $templateProcessor->setValue("middle_price_second#" . ($index + 1), $more['middle_price_second']);
            $templateProcessor->setValue("middle_price_third#" . ($index + 1), $more['middle_price_third']);
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
            'mores' => $info->mores->map(function ($more) {
                return [
                    'price_list' => $more->price_list,
                    'request_documents' => $more->request_documents,
                    'middle_price_first' => $more->middle_price_first,
                    'middle_price_second' => $more->middle_price_second,
                    'middle_price_third' => $more->middle_price_third,
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
    
        // ตรวจสอบแหล่งที่มาของเทมเพลต Word
        $templatePath = $info->template_source === 'formk' ? public_path('pcmk.docx') : public_path('pcm.docx');
    
    // คำนวณราคาสินค้ารวม
    $totalPrice = 0;
    foreach ($info->products as $product) {
        $totalPrice += $product->quantity * $product->product_price;
    }
    $data['total_price'] = $totalPrice; // เพิ่มยอดรวมใน $data

    $depositPrice = $totalPrice * 0.05;
    $data['deposit_price'] = $depositPrice; 
        // เติมข้อมูลลงในไฟล์ Word
        $outputWordPath = $this->fillWordTemplate($templatePath, $data);
    
        // ระบุไดเรกทอรีที่ต้องการเก็บไฟล์ PDF
        $outputDir = public_path(); // ไดเรกทอรี public
    
        try {
            // แปลงไฟล์ Word เป็น PDF และตั้งชื่อไฟล์เป็น "เอกสารจัดซื้อจัดจ้าง_ID.pdf"
            $pdfFilePath = $this->convertWordToPdf($outputWordPath, $outputDir, $id);
    
            // ส่งไฟล์ PDF ให้กับผู้ใช้
            return response()->file($pdfFilePath, [
                'Content-Type' => 'application/pdf',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
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

    $depositPrice = $totalPrice * 0.05;
    $data['deposit_price'] = $depositPrice; 

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
        return response()->download($filledDocumentPath, 'เอกสารจัดซื้อจัดจ้าง.docx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="เอกสารจัดซื้อจัดจ้าง.docx"',
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
        return redirect()->route('page.listpdf')->with('success', 'สร้างไฟล์สำเร็จ!');
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

public function downloadMonthlyPdf(Request $request)
{
    $month = $request->input('month'); // รับเดือนจากการเรียก
    $year = $request->input('year'); // รับปีจากการเรียก

    // เรียกใช้ฟังก์ชัน getThaiMonth
    $thaiMonth = $this->getThaiMonth($month); 

    // ดึงข้อมูลที่เกี่ยวข้องตามเดือนและปีที่เลือก
    $monthlyData = Info::with(['sellers', 'products'])
        ->whereYear('date', $year) // ตรวจสอบปี
        ->whereMonth('date', $month) // ตรวจสอบเดือน
        ->get();
    
    // คำนวณ total_price สำหรับข้อมูลที่ดึงมา
    $totalPrice = 0;
    foreach ($monthlyData as $info) {
        $infoTotal = 0;
        foreach ($info->products as $product) {
            $infoTotal += $product->quantity * $product->product_price; // คำนวณยอดรวมสำหรับแต่ละรายการ
        }
        $info->total_price = $infoTotal; // เพิ่มยอดรวมใน $info
        $totalPrice += $infoTotal; // เพิ่มยอดรวมทั้งหมด
    }

    // สร้าง PDF
    $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('สรุปรายการจัดซื้อจัดจ้างรายเดือน');
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->AddPage('L', 'A4');

    // ตั้งค่าฟอนต์
    $pdf->SetFont('thsarabunit', '', 16); // เปลี่ยนเป็นฟอนต์ที่ต้องการ

    // สร้างเนื้อหา PDF
    $htmlContent = view('pdf.monthly', compact('monthlyData', 'totalPrice', 'month', 'year', 'thaiMonth'))->render();
    $pdf->writeHTML($htmlContent, true, false, true, false, '');

    // กำหนดชื่อไฟล์ PDF
    $pdfFileName = "สรุปรายการจัดซื้อจัดจ้าง_{$thaiMonth}_{$year}.pdf"; // ใช้ $thaiMonth แทน $month

    // ส่งไฟล์ PDF ให้กับผู้ใช้เพื่อดาวน์โหลด
    return response()->stream(function() use ($pdf, $pdfFileName) {
        $pdf->Output($pdfFileName, 'D'); // 'D' หมายถึงการดาวน์โหลด
    }, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="' . $pdfFileName . '"',
    ]);
}






public function downloadQuarterlyPdf($year, $quarter)
{
    // Convert the quarter string to the actual months
    $months = [];
    switch ($quarter) {
        case '1':
            $months = [1, 2, 3]; // January to March
            break;
        case '2':
            $months = [4, 5, 6]; // April to June
            break;
        case '3':
            $months = [7, 8, 9]; // July to September
            break;
        case '4':
            $months = [10, 11, 12]; // October to December
            break;
        default:
            return redirect()->back()->with('error', 'Invalid quarter selected.');
    }

    // ดึงข้อมูลตามไตรมาสที่เลือก
    $quarterlyData = Info::with(['sellers', 'products'])
        ->whereYear('date', $year)
        ->whereIn(DB::raw('MONTH(date)'), $months)
        ->get();

    // ตรวจสอบว่ามีข้อมูลหรือไม่
    if ($quarterlyData->isEmpty()) {
        return redirect()->back()->with('error', 'ไม่มีข้อมูลสำหรับไตรมาสนี้');
    }

    // คำนวณ total_price สำหรับแต่ละรายการ
    foreach ($quarterlyData as $info) {
        $totalPrice = 0;
        foreach ($info->products as $product) {
            $totalPrice += $product->quantity * $product->product_price; // คำนวณยอดรวมสำหรับแต่ละรายการ
        }
        $info->total_price = $totalPrice; // เพิ่มยอดรวมใน $info
    }

    // สร้าง PDF โดยใช้ TCPDF
    $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('ประกาศผลผู้ชนะการจัดซื้อจัดจ้าง ประจำไตรมาส');
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->AddPage('L', 'A4');

    // ตั้งค่าฟอนต์
    $pdf->SetFont('thsarabunit', '', 16); // เปลี่ยนเป็นฟอนต์ที่ต้องการ

    // สร้างเนื้อหา PDF
    $monthRange = $this->getQuarterMonths($quarter); // Get the month range
    $htmlContent = view('pdf.quarterly', compact('quarterlyData', 'year', 'quarter', 'monthRange'))->render();
    $pdf->writeHTML($htmlContent, true, false, true, false, '');

    // กำหนดชื่อไฟล์ PDF
    $pdfFileName = "ประกาศผลผู้ชนะการจัดซื้อจัดจ้าง_ไตรมาส{$quarter}_{$monthRange}_{$year}.pdf";


    // ส่งไฟล์ PDF ให้กับผู้ใช้เพื่อดาวน์โหลด
    return response()->stream(function() use ($pdf, $pdfFileName) {
        $pdf->Output($pdfFileName, 'D'); // 'D' หมายถึงการดาวน์โหลด
    }, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="' . $pdfFileName . '"',
    ]);
}


private function getQuarterMonths($quarter) {
    switch ($quarter) {
        case 1:
            return 'มกราคม - มีนาคม';
        case 2:
            return 'เมษายน - มิถุนายน';
        case 3:
            return 'กรกฎาคม - กันยายน';
        case 4:
            return 'ตุลาคม - ธันวาคม';
        default:
            return '';
    }
}






}

