<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dich_vu', function (Blueprint $table) {
            $table->id('Ma_DichVu');
            $table->string('ten_dich_vu', 100);
            $table->decimal('don_gia', 12, 2)->default(0);
            $table->string('mo_ta', 255)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dich_vu');
    }
};
