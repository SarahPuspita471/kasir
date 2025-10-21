<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Simpan rupiah dengan 2 desimal (contoh: 12500.00)
            $table->decimal('harga_jual', 15, 2)->nullable()->after('stok_kasir');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('harga_jual');
        });
    }
};
