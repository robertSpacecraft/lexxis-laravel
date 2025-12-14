<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('material_id')
                ->constrained('materials')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('sku')->unique();

            $table->decimal('size_eu', 4, 1); //Lo hago decimal por si hubieran medias tallas
            $table->string('color_name');
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            //ïndices
            $table->index(['product_id', 'is_active']);
            $table->index(['material_id', 'size_eu']);

            //Para evitar duplicados del mismo producto con la misma combinación
            $table->unique(['product_id', 'material_id', 'size_eu', 'color_name'], 'uq_variant_combo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
