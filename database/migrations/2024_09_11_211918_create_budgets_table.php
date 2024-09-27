<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_amount', 15, 2); // งบประมาณรวม
            $table->decimal('remaining_amount', 15, 2); // งบประมาณคงเหลือ
            $table->timestamps();
        });
        
    }
};
