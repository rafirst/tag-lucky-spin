<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prizes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);                              // nama hadiah (maks 40 huruf, sama seperti input)
            $table->unsignedSmallInteger('stock')->default(1);       // stok 1..9999
            $table->boolean('is_grand')->default(false);             // true = hadiah utama (segmen merah)
            $table->unsignedInteger('sort_order')->default(0)->index(); // urutan segmen di roda
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prizes');
    }
};
