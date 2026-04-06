<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_file_analyses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('print_file_id')
                ->constrained('print_files')
                ->cascadeOnUpdate()
                ->cascadeOnDelete()
                ->unique();

            $table->decimal('estimated_volume_cm3', 10, 2)->nullable();
            $table->decimal('estimated_material_g', 10, 2)->nullable();
            $table->unsignedInteger('estimated_time_min')->nullable();

            $table->string('analysis_source', 50)->nullable();
            $table->json('dimensions_mm')->nullable();
            $table->unsignedInteger('triangle_count')->nullable();

            $table->json('analysis_details')->nullable();
            $table->boolean('manual_review_required')->default(false);
            $table->json('review_reasons')->nullable();

            $table->timestamps();

            $table->index(['analysis_source']);
            $table->index(['manual_review_required']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_file_analyses');
    }
};
