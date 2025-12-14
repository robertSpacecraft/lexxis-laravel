<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('shipping_address_id')
                ->constrained('addresses')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('billing_address_id')
                ->nullable()
                ->constrained('addresses')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('order_number')->unique();

            // Enum PHP + string en BD
            $table->string('status')->default('pending');
            $table->string('payment_status')->default('pending');

            $table->string('payment_method')->nullable();

            // Totales snapshot
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            $table->dateTime('placed_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status']);
            $table->index(['payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

