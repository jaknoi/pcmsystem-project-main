p<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('product', function (Blueprint $table) {
        $table->string('unit')->after('quantity'); // Add 'unit' column after 'quantity'
    });
}

public function down()
{
    Schema::table('product', function (Blueprint $table) {
        $table->dropColumn('unit'); // Remove 'unit' column if rolled back
    });
}

};
