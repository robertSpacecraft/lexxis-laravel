<?php

use App\Enums\ProductDesignStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_designs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('material_id')
                ->nullable()
                ->constrained('materials')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('color_name')->nullable();

            $table->decimal('size_eu', 4, 1)->nullable();

            $table->decimal('unit_price', 10, 2)->nullable();

            $table->json('pricing_breakdown')->nullable();

            $table->string('status')->default(ProductDesignStatus::Draft->value);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['product_id', 'status']);
            $table->index(['material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_designs');
    }
};
