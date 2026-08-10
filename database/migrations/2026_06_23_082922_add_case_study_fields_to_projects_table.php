<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {

            $table->text('services')->nullable();

            $table->longText('challenge')->nullable();

            $table->longText('solution')->nullable();

            $table->longText('result')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {

            $table->dropColumn([
                'services',
                'challenge',
                'solution',
                'result',
            ]);

        });
    }
};