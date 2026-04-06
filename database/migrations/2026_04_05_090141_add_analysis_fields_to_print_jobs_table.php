<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('print_jobs', function (Blueprint $table) {
            $table->unsignedSmallInteger('infill_percent')
                ->default(15)
                ->after('quantity');

            $table->unsignedSmallInteger('scale_percent')
                ->default(100)
                ->after('infill_percent');

            $table->decimal('estimated_volume_cm3', 10, 2)
                ->nullable()
                ->after('estimated_time_min');

            $table->string('analysis_source', 50)
                ->nullable()
                ->after('estimated_volume_cm3');

            $table->index(['analysis_source']);
        });
    }

    public function down(): void
    {
        Schema::table('print_jobs', function (Blueprint $table) {
            $table->dropIndex(['analysis_source']);
            $table->dropColumn([
                'infill_percent',
                'scale_percent',
                'estimated_volume_cm3',
                'analysis_source',
            ]);
        });
    }
};
