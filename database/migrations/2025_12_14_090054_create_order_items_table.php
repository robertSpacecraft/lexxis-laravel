<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Uno u otro (XOR) - lo garantizamos en la app
            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('print_job_id')
                ->nullable()
                ->constrained('print_jobs')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Snapshot del item comprado
            $table->string('item_name');

            $table->decimal('unit_price', 10, 2);
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('subtotal', 10, 2);

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['order_id']);
            $table->index(['product_variant_id']);
            $table->index(['print_job_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

