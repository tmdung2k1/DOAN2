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
        Schema::create('tien_ich', function (Blueprint $table) {
            $table->id();
            $table->string('ten_tien_ich', 100)->unique();
            $table->enum('loai', ['co_ban', 'noi_that', 'an_ninh'])->default('co_ban');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tien_ich');
    }
};
