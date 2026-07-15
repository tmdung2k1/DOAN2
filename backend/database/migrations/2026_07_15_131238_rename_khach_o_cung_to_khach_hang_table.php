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
        Schema::rename('khach_o_cung', 'khach_hang');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('khach_hang', 'khach_o_cung');
    }
};
