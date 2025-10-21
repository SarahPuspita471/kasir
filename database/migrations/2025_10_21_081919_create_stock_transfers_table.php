<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->uuid('transfer_uid')->unique();
            $table->string('kode_barang');
            $table->enum('direction', ['inbound_from_warehouse', 'outbound_to_warehouse']);
            $table->integer('qty');
            $table->enum('status', ['pending', 'committed', 'compensated', 'failed'])->default('pending');
            $table->json('warehouse_payload')->nullable();
            $table->json('warehouse_response')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
