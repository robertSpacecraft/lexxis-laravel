<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_jobs', function (Blueprint $table) {
            $table->id();

            // Propietario del job (redundancia útil para consultas y control)
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('print_file_id')
                ->constrained('print_files')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('material_id')
                ->constrained('materials')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Config
            $table->string('technology')->default('fdm');
            $table->string('color_name')->nullable();
            $table->unsignedInteger('quantity')->default(1);

            // Inputs de cálculo (estimaciones)
            $table->decimal('estimated_material_g', 10, 2)->nullable();
            $table->unsignedInteger('estimated_time_min')->nullable();

            // Snapshot pricing
            $table->decimal('unit_price', 10, 2);
            $table->json('pricing_breakdown')->nullable();

            // Estado (string + enum PHP)
            $table->string('status')->default('draft'); // draft|in_cart|ordered|printing|shipped|completed|cancelled

            $table->timestamps();
            $table->softDeletes();

            // Índices útiles
            $table->index(['user_id', 'status']);
            $table->index(['print_file_id']);
            $table->index(['material_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_jobs');
    }
};

