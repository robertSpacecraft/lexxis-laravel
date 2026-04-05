<?php

use App\Enums\CartItemType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('type')
                ->nullable()
                ->after('print_job_id');
        });

        DB::table('cart_items')
            ->whereNotNull('product_variant_id')
            ->update(['type' => CartItemType::ProductVariant->value]);

        DB::table('cart_items')
            ->whereNotNull('product_design_id')
            ->update(['type' => CartItemType::ProductDesign->value]);

        DB::table('cart_items')
            ->whereNotNull('print_job_id')
            ->update(['type' => CartItemType::PrintJob->value]);

        DB::table('cart_items')
            ->whereNull('type')
            ->update(['type' => CartItemType::ProductVariant->value]);

        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('type')->nullable(false)->change();
            $table->index(['cart_id', 'type']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex(['cart_id', 'type']);
            $table->dropIndex(['type']);
            $table->dropColumn('type');
        });
    }
};
