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
        Schema::create('faktur_penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_customer', 50)->index();
            $table->string('no_faktur')->unique();
            $table->date('tanggal_faktur');
            $table->string('pelanggan_id');
            $table->string('pengiriman_id');
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
        Schema::dropIfExists('faktur_penjualans');
    }
};
