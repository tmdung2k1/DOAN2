<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('hop_dong', function (Blueprint $table) {
            $table->dropForeign(['khach_id']);
            $table->foreign('khach_id')
                  ->references('id')
                  ->on('tai_khoans')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('hop_dong', function (Blueprint $table) {
            $table->dropForeign(['khach_id']);
            $table->foreign('khach_id')
                  ->references('id')
                  ->on('tai_khoan');
        });
    }
};
