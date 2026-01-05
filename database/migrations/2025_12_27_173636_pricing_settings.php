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
        Schema::create('pricing_settings', function (Blueprint $table) {
            $table->id();

            //Identificación / versionado
            $table->string('version');
            $table->boolean('active')->default(false);

            //Costes base
            $table->decimal('material_cost_per_kg', 10, 2);
            $table->decimal('machine_cost_per_min', 10, 4);
            $table->decimal('setup_fee_per_job', 10, 2)->default(0);

            //Multiplicadores
            $table->decimal('material_multiplier', 5, 2)->default(1.00);
            $table->decimal('time_multiplier', 5, 2)->default(1.00);
            $table->decimal('margin_multiplier', 5, 2)->default(1.00);

            //Infill (5 / 15 / 40)
            $table->json('infill_multipliers');

            $table->timestamps();

            $table->index(['active']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_settings');
    }
};
