<?php

use App\Http\Controllers\PdfController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BudgetController;
use App\Models\Seller;
use Illuminate\Http\Request;

// เส้นทางสำหรับการเข้าสู่ระบบ
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('postlogin');

// เส้นทางสำหรับการลงทะเบียน
Route::get('/signup', [AuthController::class, 'signup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signupsave'])->name('postsignup');

// เส้นทางสำหรับการออกจากระบบ
Route::post('/logout', [AuthController::class, 'signOut'])->name('logout');

// เส้นทางสำหรับการจัดการข้อมูล
Route::get('/page', [AuthController::class, 'index'])->middleware('auth');
Route::get('/page/list', [AuthController::class, 'list'])->middleware('auth')->name('page.list');
Route::get('/page/create', [AuthController::class, 'showCreateForm'])->middleware('auth')->name('page.create');
Route::get('/page/createk', [AuthController::class, 'showCreateFormk'])->middleware('auth')->name('page.createk');
Route::post('/page', [AuthController::class, 'add'])->middleware('auth');
Route::get('/page/{id}/edit', [AuthController::class, 'edit'])->middleware('auth');
Route::get('/page/{id}/editk', [AuthController::class, 'editk'])->middleware('auth');
Route::put('/page/{id}', [AuthController::class, 'update'])->middleware('auth');
Route::get('/generate-pdf/{id}', [PdfController::class, 'generatePdf'])->name('generatePdf');
Route::get('/page/listpdf', [AuthController::class, 'listpdf'])->middleware('auth')->name('page.listpdf');

// เส้นทางสำหรับการยืนยันและการสร้าง PDF
Route::get('/page/{id}/confirm', [PdfController::class, 'showConfirmation'])->name('page.confirm');
Route::post('/page/{id}/confirm', [PdfController::class, 'confirmPdfGeneration'])->name('page.confirmPdf');

Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

Route::get('/page/choiceform', function () {
    return view('page.choiceform'); // เปลี่ยนเป็นชื่อไฟล์วิวที่ถูกต้อง
})->name('page.choiceform');
Route::get('preview-pdf/{id}', [PdfController::class, 'previewPdf']);

Route::get('/page/history', [AuthController::class, 'showHistory'])->name('history');


// เส้นทางแสดงฟอร์มเพิ่มงบประมาณ (ใช้ GET)
Route::get('/budget/add', [AuthController::class, 'showAddBudgetForm'])->name('budget.add');

// เส้นทางสำหรับบันทึกงบประมาณ (ใช้ POST)
Route::post('/budget/add', [AuthController::class, 'addBudget'])->name('budget.store');

Route::get('/generate-word/{id}', [PdfController::class, 'generateWord'])->name('generate-word');

Route::get('/summary', [AuthController::class, 'summarys'])->name('summary');

