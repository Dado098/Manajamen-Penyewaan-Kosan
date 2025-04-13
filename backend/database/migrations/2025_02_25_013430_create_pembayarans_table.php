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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->string('metode_pembayaran');
            $table->decimal('total_tagihan', 15, 2);
            $table->enum('status', ['menunggu pembayaran', 'proses', 'sukses', 'gagal'])->default('menunggu pembayaran');
            $table->string('qr_code')->nullable();

            // Relasi antar table
            $table->foreignId('penyewa_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pemesanan_id')->constrained('pemesanans')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
