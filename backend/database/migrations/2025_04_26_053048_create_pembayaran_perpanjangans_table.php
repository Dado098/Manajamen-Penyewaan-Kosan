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
        Schema::create('pembayaran_perpanjangans', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique()->nullable();
            $table->string('metode_pembayaran');
            $table->decimal('total_tagihan', 15, 2);
            $table->enum('status', ['menunggu pembayaran', 'proses', 'sukses', 'gagal'])->default('menunggu pembayaran');
            $table->string('qr_code')->nullable();
            $table->string('snap_token')->nullable(); // ✅ Tambahan kolom snap_token


            // Relasi antar table
            $table->foreignId('penyewa_id')->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_perpanjangans');
    }
};
