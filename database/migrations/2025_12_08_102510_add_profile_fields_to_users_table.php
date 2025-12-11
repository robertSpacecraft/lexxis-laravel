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
        Schema::table('users', function (Blueprint $table) {
            //Datos de mi tabla users
            $table->string('last_name');
            $table->string('phone')->nullable();
            $table->string('role')->default('customer');
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //para el migrate:rollback, se eliminarán los campos en up
            $table->dropColumn(['last_name', 'phone', 'role', 'is_active']);
        });
    }
};
