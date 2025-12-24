<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Esta migración refuerza la integridad del esquema geográfico (countries, provinces,
         * cities y streets) una vez creado el modelo inicial.
         *
         * El objetivo es evitar duplicados lógicos y permitir la carga segura de catálogos
         * reales mediante seeders (países del mundo, provincias y ciudades de España),
         * sin modificar migraciones ya ejecutadas ni alterar su orden.
         *
         * Se añaden restricciones UNIQUE y se normaliza el uso de claves naturales
         * (por ejemplo, iso_code en countries) para garantizar consistencia a largo plazo.
         */

        DB::table('countries')
            ->whereNull('iso_code')
            ->update(['iso_code' => 'UNK']);

        Schema::table('countries', function (Blueprint $table) {
            $table->string('iso_code', 3)->nullable(false)->change();
        });

        Schema::table('countries', function (Blueprint $table) {
            $table->unique('iso_code');
        });

        Schema::table('provinces', function (Blueprint $table) {
            $table->unique(['country_id', 'name']);
        });


        Schema::table('cities', function (Blueprint $table) {
            $table->unique(['province_id', 'name']);
        });


        Schema::table('streets', function (Blueprint $table) {
            $table->unique(['city_id', 'name', 'street_type']);
        });
    }

    public function down(): void
    {
        Schema::table('streets', function (Blueprint $table) {
            $table->dropUnique(['city_id', 'name', 'street_type']);
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropUnique(['province_id', 'name']);
        });

        Schema::table('provinces', function (Blueprint $table) {
            $table->dropUnique(['country_id', 'name']);
        });

        Schema::table('countries', function (Blueprint $table) {
            $table->dropUnique(['iso_code']);
        });

        Schema::table('countries', function (Blueprint $table) {
            $table->string('iso_code', 3)->nullable()->change();
        });
    }
};
