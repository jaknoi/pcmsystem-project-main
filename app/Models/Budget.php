<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = ['total_amount', 'remaining_amount'];

    // Method สำหรับการเพิ่มงบประมาณ
    public function addBudget($amount)
{
    // ตรวจสอบให้แน่ใจว่าค่าเริ่มต้นเป็น 0 ถ้าเป็น null
    $this->total_amount = $this->total_amount ?? 0;
    $this->remaining_amount = $this->remaining_amount ?? 0;

    // เพิ่มงบประมาณ
    $this->total_amount += $amount;
    $this->remaining_amount += $amount; // หรือใช้การคำนวณใหม่หากต้องการ

    // บันทึกลงฐานข้อมูล
    $this->save();
}

    // Method สำหรับการรับค่าคงเหลือ (หรือค่าดีฟอลต์ถ้าเป็น null)
    public function getRemainingAmount()
    {
        return $this->remaining_amount ?? 0; // คืนค่า remaining_amount หรือ 0 ถ้าเป็น null
    }
}
