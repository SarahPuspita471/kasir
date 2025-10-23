<?php

// database/migrations/2025_10_22_000110_add_tax_columns_to_sales_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 2)->default(0)->after('voucher_discount'); // simpan % saat transaksi
            $table->unsignedBigInteger('tax_amount')->default(0)->after('tax_rate');   // rupiah pajak
            // pastikan kolom total sekarang = DPP + tax_amount
        });
    }
    public function down(): void {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['tax_rate','tax_amount']);
        });
    }
};

