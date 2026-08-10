<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('subtitle')->nullable()->after('title');
            $table->string('industry')->nullable()->after('client');
            $table->string('year')->nullable()->after('industry');
            $table->string('duration')->nullable()->after('year');
            $table->text('technologies')->nullable()->after('services');
            $table->string('accent_color', 7)->default('#22D3EE')->after('technologies');
            $table->json('gallery')->nullable()->after('mobile_thumbnail');
            $table->string('metric_one_value')->nullable()->after('result');
            $table->string('metric_one_label')->nullable()->after('metric_one_value');
            $table->string('metric_two_value')->nullable()->after('metric_one_label');
            $table->string('metric_two_label')->nullable()->after('metric_two_value');
            $table->string('metric_three_value')->nullable()->after('metric_two_label');
            $table->string('metric_three_label')->nullable()->after('metric_three_value');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'subtitle',
                'industry',
                'year',
                'duration',
                'technologies',
                'accent_color',
                'gallery',
                'metric_one_value',
                'metric_one_label',
                'metric_two_value',
                'metric_two_label',
                'metric_three_value',
                'metric_three_label',
            ]);
        });
    }
};
