<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('voucher_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // kasir yang redeem
            $table->unsignedBigInteger('order_id')->nullable();    // nanti relasi ke sales
            $table->timestamp('redeemed_at');
            $table->string('customer_ref')->nullable();            // opsional id pelanggan
            $table->decimal('amount_applied', 15, 2)->default(0);
            $table->enum('status', ['held','applied','void'])->default('held');
            $table->timestamps();

            $table->index(['voucher_id', 'user_id', 'status']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('voucher_redemptions');
    }
};
