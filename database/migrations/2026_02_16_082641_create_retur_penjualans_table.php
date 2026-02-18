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
        Schema::create('retur_penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_customer', 50)->index();
            $table->string('no_retur')->unique();
            $table->date('tanggal_retur');
            $table->string('pelanggan_id');
            $table->enum('return_type', ['delivery', 'invoice', 'invoice_dp', 'no_invoice'])->default('invoice');
            $table->enum('return_status_type', ['not_returned', 'partially_returned', 'returned'])->default('not_returned');
            $table->string('faktur_penjualan_id')->nullable();
            $table->string('pengiriman_pesanan_id')->nullable();
            $table->string('alamat')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('syarat_bayar')->nullable();
            $table->boolean('kena_pajak')->nullable();
            $table->boolean('total_termasuk_pajak')->nullable();
            $table->string('diskon_keseluruhan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retur_penjualans');
    }
};
