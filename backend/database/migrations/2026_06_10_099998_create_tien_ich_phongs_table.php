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
        Schema::create('tien_ich_phong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phong_id')->constrained('phong')->onDelete('cascade');
            $table->foreignId('tien_ich_id')->constrained('tien_ich')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tien_ich_phong');
    }
};
