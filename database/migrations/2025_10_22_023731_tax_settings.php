<?php

// database/migrations/2025_10_22_000100_create_tax_settings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('tax_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(false);
            $table->decimal('rate_percent', 5, 2)->default(0); // contoh: 11.00
            $table->string('name')->default('Pajak');
            $table->timestamps();
        });

        // seed satu baris default
        DB::table('tax_settings')->insert([
            'is_enabled'   => false,
            'rate_percent' => 0,
            'name'         => 'Pajak',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    public function down(): void {
        Schema::dropIfExists('tax_settings');
    }
};

