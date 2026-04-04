<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('product_design_id')
                ->nullable()
                ->after('product_variant_id')
                ->constrained('product_designs')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->index(['product_design_id']);
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_design_id');
        });
    }
};
