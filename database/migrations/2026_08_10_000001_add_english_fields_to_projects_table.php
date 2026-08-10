<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('title_en')->nullable()->after('title');
            $table->string('subtitle_en')->nullable()->after('subtitle');
            $table->string('industry_en')->nullable()->after('industry');
            $table->string('duration_en')->nullable()->after('duration');
            $table->text('description_en')->nullable()->after('description');
            $table->text('services_en')->nullable()->after('services');
            $table->text('challenge_en')->nullable()->after('challenge');
            $table->text('solution_en')->nullable()->after('solution');
            $table->text('result_en')->nullable()->after('result');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'subtitle_en', 'industry_en', 'duration_en', 'description_en', 'services_en', 'challenge_en', 'solution_en', 'result_en']);
        });
    }
};
