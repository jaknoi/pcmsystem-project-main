<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the Info table
        Schema::create('info', function (Blueprint $table) {
            $table->id(); // Primary key, auto-incrementing
            $table->string('methode_name');
            $table->string('office_name');
            $table->string('date');
            $table->string('reason_description');
            $table->string('devilvery_time');
            $table->timestamps();
        });

        // Create the Product table
        Schema::create('product', function (Blueprint $table) {
            $table->id(); // Primary key, auto-incrementing
            $table->string('product_type')->nullable();
            $table->string('product_name');
            $table->integer('quantity');
            $table->string('unit');
            $table->float('product_price');
            $table->foreignId('info_id')->constrained('info')->onDelete('cascade'); // Foreign key
            $table->timestamps();
        });

        // Create the Seller table
        Schema::create('seller', function (Blueprint $table) {
            $table->id(); // Primary key, auto-incrementing
            $table->string('seller_name');
            $table->string('address');
            $table->string('taxpayer_number', 13);
            $table->string('reference_documents');
            $table->foreignId('info_id')->constrained('info')->onDelete('cascade'); // Foreign key
            $table->timestamps();
        });

        // Create the CommitteeMember table
        Schema::create('committee_member', function (Blueprint $table) {
            $table->id(); // Primary key, auto-incrementing
            $table->string('member_name');
            $table->string('member_position');
            $table->foreignId('info_id')->constrained('info')->onDelete('cascade'); // Foreign key
            $table->timestamps();
        });

        // Create the Bidder table
        Schema::create('bidder', function (Blueprint $table) {
            $table->id(); // Primary key, auto-incrementing
            $table->string('bidder_name');
            $table->string('bidder_position');
            $table->foreignId('info_id')->constrained('info')->onDelete('cascade'); // Foreign key
            $table->timestamps();
        });

        // Create the Inspector table
        Schema::create('inspector', function (Blueprint $table) {
            $table->id(); // Primary key, auto-incrementing
            $table->string('inspector_name');
            $table->string('inspector_position');
            $table->foreignId('info_id')->constrained('info')->onDelete('cascade'); // Foreign key
            $table->timestamps();
        });
        Schema::table('info', function (Blueprint $table) {
            $table->string('template_source')->nullable(); // เก็บค่า 'form' หรือ 'formk'
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspector');
        Schema::dropIfExists('bidder');
        Schema::dropIfExists('committee_member');
        Schema::dropIfExists('seller');
        Schema::dropIfExists('product');
        Schema::dropIfExists('info');
    }
};
