<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('customer_id')->unique();
            $table->string('url_accurate')->nullable();
            $table->text('auth_accurate')->nullable();
            $table->text('session_accurate')->nullable();
            $table->text('accurate_api_token')->nullable();
            $table->text('accurate_signature_secret')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
