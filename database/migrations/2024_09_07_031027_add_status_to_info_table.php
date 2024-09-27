<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('info', function (Blueprint $table) {
        $table->string('status')->default('Pending'); // หรือค่าเริ่มต้นที่คุณต้องการ
    });
}

public function down()
{
    Schema::table('info', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}

};
