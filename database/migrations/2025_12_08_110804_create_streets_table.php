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
        Schema::create('streets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('street_type')->nullable();
            $table->foreignId('city_id')
                ->constrained('cities')
                ->cascadeOnUpdate() // Actualiza las tablas dependientes
                ->restrictOnDelete(); // Evita ser borrada si tiene datos dependientes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('streets');
    }
};
