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
        Schema::create('approval_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->unique();
            $table->string('kode_customer', 50)->index();
            $table->string('nama')->nullable();
            $table->string('npl')->nullable();
            $table->string('no_invoice')->nullable();
            $table->string('kontrak')->nullable();
            $table->string('id_pb')->nullable();
            $table->string('panjang')->nullable();
            $table->string('harga_unit')->nullable();
            $table->enum('status', ['draft', 'approved', 'uploaded'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_stocks');
    }
};
