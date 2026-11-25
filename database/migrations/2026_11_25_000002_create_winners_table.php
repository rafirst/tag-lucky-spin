<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('winners', function (Blueprint $table) {
            $table->id();
            // Kalau hadiahnya dihapus dari roda, riwayat tetap ada (prize_id jadi NULL).
            $table->foreignId('prize_id')->nullable()->constrained('prizes')->nullOnDelete();
            $table->string('prize_name', 40);                        // salinan nama hadiah saat menang
            $table->boolean('is_grand')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('winners');
    }
};
