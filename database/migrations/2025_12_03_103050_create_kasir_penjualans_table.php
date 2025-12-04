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
        Schema::create('kasir_penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_customer', 50)->index();
            $table->string('npj');
            $table->string('customer');
            $table->date('tanggal');
            $table->string('alamat')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('syarat_bayar')->nullable();
            $table->string('diskon_keseluruhan')->nullable();
            $table->boolean('kena_pajak')->nullable();
            $table->boolean('total_termasuk_pajak')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kasir_penjualans');
    }
};
