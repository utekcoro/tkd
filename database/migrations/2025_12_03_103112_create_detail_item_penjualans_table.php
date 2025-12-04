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
        Schema::create('detail_item_penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_customer', 50)->index();
            $table->string('barcode')->unique();
            $table->string('npj');
            $table->string('qty');
            $table->string('harga');
            $table->string('diskon');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_item_penjualans');
    }
};
