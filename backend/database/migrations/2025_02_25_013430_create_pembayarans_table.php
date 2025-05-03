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
            $table->string('order_id')->unique()->nullable(); // Order ID yang dikirim ke Midtrans
            $table->string('metode_pembayaran')->nullable(); // Contoh: bank_transfer, gopay, qris
            $table->string('qr_code')->nullable(); // Jika pakai QRIS
            $table->string('snap_token')->nullable(); // Snap token yang dikirim ke front-end

            $table->decimal('total_tagihan', 15, 2);

            $table->enum('status', ['menunggu pembayaran', 'proses', 'sukses', 'gagal'])->default('menunggu pembayaran');


            // Relasi antar table
            $table->foreignId('penyewa_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pemesanan_id')->nullable()->constrained('pemesanans')->onDelete('cascade');
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
