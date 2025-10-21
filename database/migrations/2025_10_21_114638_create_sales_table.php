<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sales', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();              // nomor struk
            $t->unsignedBigInteger('user_id');         // kasir (users.id)
            $t->string('customer_name')->nullable();   // nama pembeli (opsional)

            $t->unsignedBigInteger('subtotal');        // dalam rupiah
            $t->unsignedBigInteger('auto_discount')->default(0);
            $t->unsignedBigInteger('voucher_discount')->default(0);
            $t->unsignedBigInteger('total');           // subtotal - diskon

            $t->unsignedBigInteger('cash_paid')->default(0);
            $t->unsignedBigInteger('change_due')->default(0);

            // info voucher (jika dipakai)
            $t->foreignId('voucher_redemption_id')->nullable()->constrained()->nullOnDelete();
            $t->string('voucher_code')->nullable();

            // simpan ringkasan diskon (agar historis tidak berubah jika scheme/voucher berubah)
            $t->json('discount_snapshot')->nullable();

            $t->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('sales');
    }
};
