<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sale_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();

            // snapshot produk saat transaksi
            $t->string('kode_barang');
            $t->string('nama_barang');
            $t->unsignedBigInteger('harga_jual');
            $t->unsignedInteger('qty');
            $t->unsignedBigInteger('line_total');

            $t->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('sale_items');
    }
};
