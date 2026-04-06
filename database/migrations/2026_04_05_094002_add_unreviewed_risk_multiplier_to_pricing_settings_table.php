<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pricing_settings', function (Blueprint $table) {
            $table->decimal('unreviewed_risk_multiplier', 5, 2)
                ->default(1.25)
                ->after('risk_multiplier');
        });
    }

    public function down(): void
    {
        Schema::table('pricing_settings', function (Blueprint $table) {
            $table->dropColumn('unreviewed_risk_multiplier');
        });
    }
};
