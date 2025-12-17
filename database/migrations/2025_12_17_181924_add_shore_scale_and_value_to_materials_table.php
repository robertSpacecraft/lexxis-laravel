<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->string('shore_scale', 2)->nullable()->after('shore_a'); // '00', 'A', 'D'
            $table->unsignedSmallInteger('shore_value')->nullable()->after('shore_scale');
            $table->index(['shore_scale', 'shore_value']);
        });

        // Backfill: si había shore_a, asumimos escala 'A'
        DB::table('materials')
            ->whereNotNull('shore_a')
            ->update([
                'shore_scale' => 'A',
                'shore_value' => DB::raw('shore_a'),
            ]);
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropIndex(['shore_scale', 'shore_value']);
            $table->dropColumn(['shore_scale', 'shore_value']);
        });
    }
};

