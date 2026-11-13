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
        Schema::create('log_gerbangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perizinan_id')->constrained()->onDelete('cascade');
            $table->foreignId('satpam_id')->constrained('users')->onDelete('cascade'); // Petugas satpam yang scan/validasi
            $table->enum('tipe_scan', ['keluar', 'masuk']);
            $table->timestamp('waktu_scan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_gerbangs');
    }
};
