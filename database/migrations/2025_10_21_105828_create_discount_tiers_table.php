<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('discount_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_scheme_id')->constrained()->cascadeOnDelete();
            $table->decimal('min_subtotal', 15, 2);               // ambang
            $table->enum('type', ['percent', 'amount']);          // % atau Rp
            $table->decimal('value', 10, 2);                      // 0–100 utk percent
            $table->unsignedInteger('priority')->default(0);      // makin besar makin prioritas
            $table->timestamps();

            $table->index(['discount_scheme_id', 'min_subtotal']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('discount_tiers');
    }
};
