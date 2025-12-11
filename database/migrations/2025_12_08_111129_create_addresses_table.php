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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            //Relación con el usuario
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate() //Actualiza las tablas dependientes
                ->restrictOnDelete(); // Restringe su eliminación si tiene datos dependientes

            //Relación con calle
            $table->foreignId('street_id')
                ->constrained('streets')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            //Datos de la dirección
            $table->string('alias')->nullable();
            $table->string('street_number');
            $table->string('floor')->nullable();
            $table->string('door')->nullable();
            $table->string('address_type')->default('shipping');
            $table->string('contact_phone')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
