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
        Schema::create('retur_pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('kode_customer', 50)->index();
            $table->string('no_retur')->unique();
            $table->date('tanggal_retur');
            $table->string('vendor');
            $table->enum('return_type', ['invoice', 'invoice_dp', 'no_invoice', 'receive'])->default('invoice');
            $table->string('faktur_pembelian_id')->nullable();
            $table->string('penerimaan_barang_id')->nullable();
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
        Schema::dropIfExists('retur_pembelians');
    }
};
